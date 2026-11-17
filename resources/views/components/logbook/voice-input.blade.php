@props(['target'])

<!-- Voice-to-Log: tombol dikte. Hasil rekam ditampilkan di panel pratinjau (x-logbook.voice-preview) sebelum masuk deskripsi.
     Browser dengan Web Speech API memakai pengenalan suara bawaan, browser lain merekam audio lalu ditranskripsikan di server (Gemini). -->
<div data-voice-root class="shrink-0">
    <button type="button"
            data-voice-toggle
            data-target="{{ $target }}"
            class="h-9 bg-white hover:bg-[#F4F7F6] text-[#074745] text-xs font-semibold px-4 rounded-full flex items-center gap-2 shadow-xs transition disabled:opacity-60">
        <i data-voice-icon class="fa-solid fa-microphone-lines text-[#0B6E6C] text-xs"></i>
        <span data-voice-label class="whitespace-nowrap">Rekam Suara</span>
    </button>
</div>

@once
    <script>
        (function () {
            const SpeechRecognitionApi = window.SpeechRecognition || window.webkitSpeechRecognition;
            const canRecordAudio = Boolean(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && window.MediaRecorder);
            const activeSessions = new WeakMap();

            const SPEECH_ERROR_MESSAGES = {
                'not-allowed': 'Izin mikrofon ditolak. Aktifkan izin mikrofon di browser.',
                'service-not-allowed': 'Pengenalan suara tidak diizinkan di browser ini.',
                'no-speech': 'Tidak ada suara terdeteksi. Coba lagi.',
                'audio-capture': 'Mikrofon tidak ditemukan.',
                'network': 'Pengenalan suara butuh koneksi internet.',
            };

            function getPreview(targetId) {
                return document.getElementById(`voice-preview-${targetId}`);
            }

            function renderPreview(preview, { text = '', state = '', error = '', showActions = false }) {
                preview.classList.remove('hidden');
                preview.querySelector('[data-voice-preview-state]').textContent = state;
                preview.querySelector('[data-voice-preview-text]').textContent = text;

                const errorEl = preview.querySelector('[data-voice-preview-error]');
                errorEl.textContent = error;
                errorEl.classList.toggle('hidden', error === '');

                const actionsEl = preview.querySelector('[data-voice-preview-actions]');
                actionsEl.classList.toggle('hidden', !showActions);
                actionsEl.classList.toggle('flex', showActions);
            }

            function hidePreview(preview) {
                preview.classList.add('hidden');
                preview.querySelector('[data-voice-preview-text]').textContent = '';
                preview.querySelector('[data-voice-preview-error]').classList.add('hidden');
                preview.querySelector('[data-voice-preview-actions]').classList.add('hidden');
            }

            function setButtonState(button, state) {
                const icon = button.querySelector('[data-voice-icon]');
                const label = button.querySelector('[data-voice-label]');
                button.disabled = state === 'busy';

                if (state === 'listening') {
                    icon.className = 'fa-solid fa-stop text-[#B91C1C] text-xs';
                    label.textContent = 'Berhenti';
                    button.classList.add('ring-2', 'ring-[#FCE1D4]');
                    return;
                }

                if (state === 'busy') {
                    icon.className = 'fa-solid fa-spinner fa-spin text-[#0B6E6C] text-xs';
                    label.textContent = 'Memproses...';
                    button.classList.remove('ring-2', 'ring-[#FCE1D4]');
                    return;
                }

                icon.className = 'fa-solid fa-microphone-lines text-[#0B6E6C] text-xs';
                label.textContent = 'Rekam Suara';
                button.classList.remove('ring-2', 'ring-[#FCE1D4]');
            }

            function joinText(existing, addition) {
                const cleanedAddition = (addition || '').trim();
                if (cleanedAddition === '') {
                    return existing;
                }
                return existing.trim() === '' ? cleanedAddition : `${existing.trimEnd()} ${cleanedAddition}`;
            }

            function startSpeechRecognition(button, targetId, preview) {
                const recognition = new SpeechRecognitionApi();
                recognition.lang = 'id-ID';
                recognition.continuous = true;
                recognition.interimResults = true;

                let finalText = '';
                let errorMessage = '';

                recognition.onresult = (event) => {
                    let interimText = '';
                    for (let index = event.resultIndex; index < event.results.length; index++) {
                        const result = event.results[index];
                        if (result.isFinal) {
                            finalText = joinText(finalText, result[0].transcript);
                        } else {
                            interimText += result[0].transcript;
                        }
                    }
                    renderPreview(preview, { text: joinText(finalText, interimText), state: 'Mendengarkan...' });
                };

                recognition.onerror = (event) => {
                    errorMessage = SPEECH_ERROR_MESSAGES[event.error] || 'Rekam suara gagal. Silakan ketik manual.';
                };

                recognition.onend = () => {
                    if (activeSessions.get(button) !== recognition) {
                        return;
                    }
                    activeSessions.delete(button);
                    setButtonState(button, 'idle');

                    if (finalText.trim() === '') {
                        renderPreview(preview, { error: errorMessage || 'Tidak ada suara terdeteksi. Coba lagi.', state: 'Gagal' });
                        return;
                    }
                    renderPreview(preview, { text: finalText, state: 'Selesai', showActions: true });
                    pendingTranscripts.set(targetId, finalText);
                };

                activeSessions.set(button, recognition);
                setButtonState(button, 'listening');
                renderPreview(preview, { text: '', state: 'Mendengarkan...' });
                recognition.start();
            }

            async function startAudioRecording(button, targetId, preview) {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const chunks = [];
                const recorder = new MediaRecorder(stream);

                recorder.ondataavailable = (event) => {
                    if (event.data && event.data.size > 0) {
                        chunks.push(event.data);
                    }
                };

                recorder.onstop = async () => {
                    stream.getTracks().forEach((track) => track.stop());
                    activeSessions.delete(button);
                    const audioBlob = new Blob(chunks, { type: recorder.mimeType || 'audio/webm' });
                    await uploadForTranscript(button, targetId, preview, audioBlob);
                };

                activeSessions.set(button, recorder);
                setButtonState(button, 'listening');
                renderPreview(preview, { text: '', state: 'Merekam...' });
                recorder.start();
            }

            async function uploadForTranscript(button, targetId, preview, audioBlob) {
                const formData = new FormData();
                formData.append('audio', audioBlob, `rekaman.${audioBlob.type.includes('mp4') ? 'mp4' : 'webm'}`);

                setButtonState(button, 'busy');
                renderPreview(preview, { text: '', state: 'Mentranskripsi...' });

                try {
                    const response = await fetch('/api/ai/transcribe', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: formData,
                        credentials: 'same-origin',
                    });
                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        renderPreview(preview, { error: payload.message || 'Transkripsi gagal. Silakan ketik manual.', state: 'Gagal' });
                        return;
                    }

                    const transcript = (payload.transcript || '').trim();
                    if (transcript === '') {
                        renderPreview(preview, { error: 'Tidak ada teks yang terdeteksi. Coba lagi.', state: 'Gagal' });
                        return;
                    }

                    pendingTranscripts.set(targetId, transcript);
                    renderPreview(preview, { text: transcript, state: 'Selesai', showActions: true });
                } catch (error) {
                    renderPreview(preview, { error: 'Tidak bisa terhubung ke server. Periksa koneksi lalu coba lagi.', state: 'Gagal' });
                } finally {
                    setButtonState(button, 'idle');
                }
            }

            const pendingTranscripts = new Map();

            document.addEventListener('click', async (event) => {
                const applyButton = event.target.closest('[data-voice-apply]');
                if (applyButton) {
                    const preview = applyButton.closest('[id^="voice-preview-"]');
                    const targetId = preview.id.replace('voice-preview-', '');
                    const textarea = document.getElementById(targetId);
                    if (textarea) {
                        textarea.value = joinText(textarea.value, pendingTranscripts.get(targetId) || '');
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    pendingTranscripts.delete(targetId);
                    hidePreview(preview);
                    return;
                }

                const discardButton = event.target.closest('[data-voice-discard]');
                if (discardButton) {
                    const preview = discardButton.closest('[id^="voice-preview-"]');
                    pendingTranscripts.delete(preview.id.replace('voice-preview-', ''));
                    hidePreview(preview);
                    return;
                }

                const button = event.target.closest('[data-voice-toggle]');
                if (!button) {
                    return;
                }

                const targetId = button.dataset.target;
                const preview = getPreview(targetId);
                if (!preview) {
                    return;
                }

                const activeSession = activeSessions.get(button);
                if (activeSession) {
                    activeSession.stop();
                    return;
                }

                pendingTranscripts.delete(targetId);

                if (SpeechRecognitionApi) {
                    startSpeechRecognition(button, targetId, preview);
                    return;
                }

                if (canRecordAudio) {
                    try {
                        await startAudioRecording(button, targetId, preview);
                    } catch (error) {
                        renderPreview(preview, { error: 'Mikrofon tidak bisa diakses. Izinkan mikrofon atau ketik manual.', state: 'Gagal' });
                    }
                    return;
                }

                renderPreview(preview, { error: 'Browser ini tidak mendukung rekam suara. Silakan ketik manual.', state: 'Gagal' });
            });
        })();
    </script>
@endonce
