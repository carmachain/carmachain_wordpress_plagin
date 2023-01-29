'use strict';

/**
 *   The following code will show the Popup, when DOMContentLoaded happens
 */
window.addEventListener('DOMContentLoaded', () => {
    let popupIFrame = document.createElement("iframe");
    popupIFrame.setAttribute('id', 'carmachain-popup');
    popupIFrame.setAttribute('src', carmachain.afterOrderPopupUrl);
    document.body.appendChild(popupIFrame);
});

/**
 *   This is listener, which hides the Popup
 *   To close the popup use the following code from IFrame page:
 *   window.postMessage('carmachain-popup-close');
 */
window.addEventListener('message', (event) => {
    if (
            event.origin !== carmachain.afterOrderPopupUrlOrigin
        &&  event.data   === 'carmachain-popup-close'
    ) {
        let popupIFrame = document.getElementById('carmachain-popup');
        if (popupIFrame) {
            popupIFrame.remove();
        }
    }
});