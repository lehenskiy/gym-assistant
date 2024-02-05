function copyText(elementId) {
    let elementText = document.getElementById(elementId).textContent;
    navigator.clipboard.writeText(elementText);
}
