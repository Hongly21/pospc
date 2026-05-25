document.addEventListener('DOMContentLoaded', function () {
    const imageUpload = document.getElementById('imageUpload');
    if (!imageUpload) return;

    imageUpload.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const imgElement = document.querySelector('.card-body img, .card-body .rounded-circle');
            if (!imgElement) return;

            if (imgElement.tagName.toLowerCase() === 'img') {
                imgElement.src = e.target.result;
            } else {
                imgElement.style.backgroundImage = `url(${e.target.result})`;
                imgElement.textContent = '';
            }
        };
        reader.readAsDataURL(file);
    });
});
