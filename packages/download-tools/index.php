<div class="contents-container-1">
    <div id="dynamic-videos"></div>
</div>

<script>
    jQuery(document).ready(function($) {
        const container = $('#dynamic-videos');

        fetch('https://videomat.org/filestream/kraeftner/videolist.php')
            .then(res => res.json())
            .then(videos => {
                if (!videos.length) {
                    container.append('<p>No videos found.</p>');
                    return;
                }

                const file = videos[0]; // Nur EIN Testvideo nehmen
                const title = file.replace('.mp4', '').replace(/[_-]/g, ' ');
                const videoURL = `https://videomat.org/filestream/kraeftner/bootlegs-mp4/${file}`;
                const id = file.replace('.mp4', '').toLowerCase();

                const html = `
                <h2 id="${id}">${title}</h2>
                <div class="video-container3">
                    <video class="video" controls width="800" height="auto" preload="metadata">
                        <source src="${videoURL}">
                    </video>
                </div>
            `;
                container.append(html);

                // Dein bestehender Observer + Play/Pause-Handling
                const video = container.find('video')[0];

                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !video.loaded) {
                            video.load();
                            video.loaded = true;
                        } else if (!entry.isIntersecting && !video.paused) {
                            video.pause();
                        }
                    });
                }, {
                    threshold: 0.1
                });

                observer.observe(video);

                video.addEventListener('loadedmetadata', () => video.pause());
                let manuallyPlayed = false;
                video.addEventListener('play', () => manuallyPlayed = true);
                video.addEventListener('pause', () => manuallyPlayed = false);
                video.addEventListener('click', () => manuallyPlayed = true);
            });
    });
</script>