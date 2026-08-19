/* VTO Engine for Jewelry (MediaPipe + Three.js) */
(function($) {
    'use strict';

    const VTO = {
        params: typeof ahnira_vto_params !== 'undefined' ? ahnira_vto_params : null,
        video: null,
        canvas: null,
        renderer: null,
        scene: null,
        camera: null,
        model: null,
        vision: null,
        detector: null,
        isRunning: false,
        results: null,

        init() {
            if (!this.params) return;
            $(document).on('click', '#ahnira-start-vto', (e) => {
                e.preventDefault();
                this.start();
            });
        },

        async start() {
            try {
                // 1. Request Camera Access FIRST
                const constraints = {
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: "user"
                    }
                };

                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia(constraints);
                } catch (camError) {
                    console.error('Camera Access Error:', camError);
                    if (camError.name === 'NotAllowedError' || camError.name === 'PermissionDeniedError') {
                        alert('Camera access is required for the Virtual Try-On experience. Please enable it in your browser settings and try again.');
                    } else {
                        alert('Could not access camera. Please ensure your camera is connected and not being used by another application.');
                    }
                    return;
                }

                // 2. Create Overlay only if camera is granted
                this.createOverlay();
                this.video.srcObject = stream;

                // 3. Initialize AI and 3D Engine
                await Promise.all([
                    this.initThreeJS(),
                    this.initVision(),
                    new Promise(resolve => {
                        if (this.video.readyState >= 2) resolve();
                        else this.video.onloadedmetadata = resolve;
                    })
                ]);

                this.isRunning = true;
                $('.vto-loader').fadeOut();
                this.animate();
            } catch (error) {
                console.error('VTO Initialization Error:', error);
                alert('Could not start Virtual Try-On. A technical error occurred while setting up the AR room. Please try again.');
                this.stop();
            }
        },

        createOverlay() {
            const html = `
                <div class="vto-overlay">
                    <button class="vto-close">&times;</button>
                    <div class="vto-container">
                        <video id="vto-video" playsinline muted></video>
                        <canvas id="vto-canvas"></canvas>
                        <div class="vto-loader">
                            <div class="spinner"></div>
                            <p>Initializing AR Room...</p>
                        </div>
                        <div class="vto-instructions">Focus on your ${this.params.vto_type === 'face' ? 'face' : 'hand'}</div>
                    </div>
                </div>
            `;
            $('body').append(html).addClass('vto-active');
            $('.vto-overlay').css('display', 'flex').hide().fadeIn();
            
            this.video = document.getElementById('vto-video');
            this.canvas = document.getElementById('vto-canvas');
            
            $('.vto-close').one('click', () => this.stop());
        },

        async initThreeJS() {
            if (typeof THREE === 'undefined') {
                throw new Error('Three.js library failed to load. Please check your internet connection.');
            }
            this.scene = new THREE.Scene();
            this.camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 1000);
            this.renderer = new THREE.WebGLRenderer({ 
                canvas: this.canvas, 
                alpha: true, 
                antialias: true 
            });
            this.renderer.setSize(window.innerWidth, window.innerHeight);
            this.renderer.setPixelRatio(window.devicePixelRatio);

            const ambientLight = new THREE.AmbientLight(0xffffff, 1.5);
            this.scene.add(ambientLight);

            const light = new THREE.DirectionalLight(0xffffff, 1);
            light.position.set(0, 1, 2);
            this.scene.add(light);

            // Load Model
            return new Promise((resolve, reject) => {
                const loader = new THREE.GLTFLoader();
                loader.load(this.params.model_url, (gltf) => {
                    this.model = gltf.scene;
                    this.model.visible = false;
                    this.scene.add(this.model);
                    resolve();
                }, undefined, reject);
            });
        },

        async initVision() {
            // Load MediaPipe from CDN dynamically
            const vision = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3');
            this.vision = vision;
            
            const wasmLoader = {
               locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm/${file}`
            };

            const fileset = await vision.FilesetResolver.forVisionTasks(wasmLoader.locateFile);
            
            if (this.params.vto_type === 'face') {
                this.detector = await vision.FaceLandmarker.createFromOptions(fileset, {
                    baseOptions: {
                        modelAssetPath: `https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task`,
                        delegate: "GPU"
                    },
                    runningMode: "VIDEO",
                    numFaces: 1
                });
            } else {
                this.detector = await vision.HandLandmarker.createFromOptions(fileset, {
                    baseOptions: {
                        modelAssetPath: `https://storage.googleapis.com/mediapipe-models/hand_landmarker/hand_landmarker/float16/1/hand_landmarker.task`,
                        delegate: "GPU"
                    },
                    runningMode: "VIDEO",
                    numHands: 1
                });
            }
        },



        animate() {
            if (!this.isRunning) return;
            requestAnimationFrame(() => this.animate());

            if (this.video.readyState >= 2) {
                const startTimeMs = performance.now();
                this.results = this.detector.detectForVideo(this.video, startTimeMs);
                this.updateModelPosition();
            }

            this.renderer.render(this.scene, this.camera);
        },

        updateModelPosition() {
            if (!this.results || !this.model) return;

            if (this.params.vto_type === 'face' && this.results.faceLandmarks && this.results.faceLandmarks.length > 0) {
                this.model.visible = true;
                const landmarks = this.results.faceLandmarks[0];
                
                // Example mapping (Earring position - around ears)
                // Left Ear: Index 234, Right Ear: Index 454 (standard MediaPipe face mesh)
                const noseBridge = landmarks[1];
                this.model.position.set(
                    (noseBridge.x - 0.5) * 10,
                    -(noseBridge.y - 0.5) * 10,
                    -noseBridge.z * 10
                );
                // Rotation logic would go here
            } else if (this.params.vto_type === 'hand' && this.results.landmarks && this.results.landmarks.length > 0) {
                this.model.visible = true;
                const landmarks = this.results.landmarks[0];
                const wrist = landmarks[0];
                this.model.position.set(
                    (wrist.x - 0.5) * 10,
                    -(wrist.y - 0.5) * 10,
                    -wrist.z * 10
                );
            } else {
                this.model.visible = false;
            }
        },

        stop() {
            this.isRunning = false;
            $('body').removeClass('vto-active');
            $('.vto-overlay').fadeOut(() => $('.vto-overlay').remove());
            if (this.video && this.video.srcObject) {
                this.video.srcObject.getTracks().forEach(track => track.stop());
            }
        }
    };

    $(document).ready(() => VTO.init());

})(jQuery);
