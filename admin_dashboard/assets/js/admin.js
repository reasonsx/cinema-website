document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = (container, inputName) => {
        container.addEventListener('click', e => {
            const target = e.target;
            if (!target.classList.contains('actor-btn') && !target.classList.contains('director-btn')) return;
            target.classList.toggle('selected');
            container.querySelectorAll('input[type="hidden"]').forEach(el => el.remove());
            Array.from(container.querySelectorAll('.selected')).forEach(el => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputName;
                input.value = el.dataset.id;
                container.appendChild(input);
            });
        });
    };
    const actorContainer = document.getElementById('actors-container');
    const directorContainer = document.getElementById('directors-container');
    if (actorContainer) toggleButtons(actorContainer, 'actors[]');
    if (directorContainer) toggleButtons(directorContainer, 'directors[]');
});
