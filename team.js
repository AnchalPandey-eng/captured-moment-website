function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('preview-image');
        output.src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}

function openModal(imageSrc) {
    var modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100vw';
    modal.style.height = '100vh';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
    modal.style.display = 'flex';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';
    modal.onclick = function() {
        document.body.removeChild(modal);
    };
    var img = document.createElement('img');
    img.src = imageSrc;
    img.style.maxWidth = '80%';
    img.style.maxHeight = '80%';
    modal.appendChild(img);
    document.body.appendChild(modal);
}
