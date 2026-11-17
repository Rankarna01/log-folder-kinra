@props(['target'])

<!-- Voice-to-Log: dikte langsung ke textarea deskripsi. Browser dengan Web Speech API memakai pengenalan suara bawaan,
     browser lain merekam audio lalu ditranskripsikan oleh layanan Gemini di server (butuh GEMINI_API_KEY di .env). -->
<div data-voice-root class="shrink-0 flex flex-col gap-1">
    <button type="button"
            data-voice-toggle
            data-target="{{ $target }}"
            class="h-9 bg-white hover:bg-[#F4F7F6] text-[#074745] text-xs font-semibold px-4 rounded-full flex items-center gap-2 shadow-xs transition disabled:opacity-60">
        <i data-voice-icon class="fa-solid fa-microphone-lines text-[#0B6E6C] text-xs"></i>
        <span data-voice-label class="whitespace-nowrap">Rekam Suara</span>
    </button>
    <p data-voice-status aria-live="polite" class="hidden text-[11px] font-medium text-white/90 px-1"></p>
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

            function getRoot(button) {
                return button.closest('[data-voice-root]');
            }

            function setStatus(button, message, tone) {
                const statusEl = getRoot(button).querySelector('[data-voice-status]');
                statusEl.textContent = message;
                statusEl.classList.remove('hidden', 'text-white/90', 'text-[#FDE9B4]', 'text-[#FCE1D4]');
                statusEl.classList.add(tone === 'error' ? 'text-[#FCE1D4]' : 'text-white/90');
            }

            function clearStatus(button) {
                getRoot(button).querySelector('[data-voice-status]').classList.add('hidden');
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

            function appendTranscript(textarea, transcript) {
                const cleaned = (transcript || '').trim();
                if (cleaned === '') {
                    return;
                }

                const existing = textarea.value.trimEnd();
                textarea.value = existing === '' ? cleaned : `${existing} ${cleaned}`;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }

            function startSpeechRecognition(button, textarea) {
                const recognition = new SpeechRecognitionApi();
                recognition.lang = 'id-ID';
                recognition.continuous = true;
                recognition.interimResults = true;

                recognition.onresult = (event) => {
                    let interimText = '';
                    for (let index = event.resultIndex; index < event.results.length; index++) {
                        const result = event.results[index];
                        if (result.isFinal) {
                            appendTranscript(textarea, result[0].transcript);
                        } else {
                            interimText += result[0].transcript;
                        }
                    }
                    setStatus(button, interimText ? `Mendengarkan: ${interimText}` : 'Mendengarkan...', 'info');
                };

                recognition.onerror = (event) => {
                    setStatus(button, SPEECH_ERROR_MESSAGES[event.error] || 'Rekam suara gagal. Silakan ketik manual.', 'error');
                };

                recognition.onend = () => {
                    if (activeSessions.get(button) === recognition) {
                        activeSessions.delete(button);
                        setButtonState(button, 'idle');
                        clearStatus(button);
                    }
                };

                activeSessions.set(button, recognition);
                setButtonState(button, 'listening');
                setStatus(button, 'Mendengarkan... ketuk Berhenti jika sudah selesai.', 'info');
                recognition.start();
            }

            async function startAudioRecording(button, textarea) {
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
                    await uploadForTranscript(button, textarea, audioBlob);
                };

                activeSessions.set(button, recorder);
                setButtonState(button, 'listening');
                setStatus(button, 'Merekam... ketuk Berhenti jika sudah selesai.', 'info');
                recorder.start();
            }

            async function uploadForTranscript(button, textarea, audioBlob) {
                const formData = new FormData();
                formData.append('audio', audioBlob, `rekaman.${audioBlob.type.includes('mp4') ? 'mp4' : 'webm'}`);

                setButtonState(button, 'busy');
                setStatus(button, 'Mentranskripsi rekaman...', 'info');

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
                        setStatus(button, payload.message || 'Transkripsi gagal. Silakan ketik manual.', 'error');
                        return;
                    }

                    appendTranscript(textarea, payload.transcript);
                    setStatus(button, 'Teks rekaman sudah ditambahkan ke deskripsi.', 'info');
                } catch (error) {
                    setStatus(button, 'Tidak bisa terhubung ke server. Periksa koneksi lalu coba lagi.', 'error');
                } finally {
                    setButtonState(button, 'idle');
                }
            }

            document.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-voice-toggle]');
                if (!button) {
                    return;
                }

                const textarea = document.getElementById(button.dataset.target);
                if (!textarea) {
                    return;
                }

                const activeSession = activeSessions.get(button);
                if (activeSession) {
                    activeSession.stop();
                    return;
                }

                clearStatus(button);

                if (SpeechRecognitionApi) {
                    startSpeechRecognition(button, textarea);
                    return;
                }

                if (canRecordAudio) {
                    try {
                        await startAudioRecording(button, textarea);
                    } catch (error) {
                        setStatus(button, 'Mikrofon tidak bisa diakses. Izinkan mikrofon atau ketik manual.', 'error');
                    }
                    return;
                }

                setStatus(button, 'Browser ini tidak mendukung rekam suara. Silakan ketik manual.', 'error');
            });
        })();
    </script>
@endonce
