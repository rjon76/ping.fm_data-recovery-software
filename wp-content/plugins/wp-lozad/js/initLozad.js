class InitLozad {
    // verify the existence of the file
    constructor() {
        this.useIfFileExists = false; // todo handle
    }

    init() {
        this.handle();
    }

    handle() {
        if (typeof lozad !== 'function') {
            this.image2x();
            return;
        }

        this.initLazyload();
    }

    initLazyload() {
        lozad('.lazyload', {
            loaded: (el) => {
                el.classList.remove('lazyload');
            }
        }).observe();

        this.image2x();

        window.load = function() {
            window.scrollTo(window.scrollX, window.scrollY);
        };
    }

    urlExists(url) {
        const http = new XMLHttpRequest();
        http.open('HEAD', url, false);
        http.send();
        return http.status !== 404;
    }

    replaceImgTo2Img(currentString) {
        if (!currentString) {
            return '';
        }

        return currentString.match('@2x')
            ? currentString
            : currentString.replace('.png', '@2x.png').replace('.jpg', '@2x.jpg').replace('.webp', '@2x.webp');
    }

    handleBgImageTo2xBg() {
        let o = this;

        const replaceBgImageTo2xBg = (element) => {
            element.setAttribute('data-background-image', o.replaceImgTo2Img(element.getAttribute('data-background-image')));
        };

        document.querySelectorAll('.image2x[data-background-image]').forEach((element) => {
            let dataBackgroundImage = element.getAttribute('data-background-image');

            if (o.useIfFileExists) {
                if (o.urlExists(o.replaceImgTo2Img(dataBackgroundImage.substring(5, (dataBackgroundImage.length - 2))))) {
                    replaceBgImageTo2xBg(element);
                }
            } else {
                replaceBgImageTo2xBg(element);
            }
            return true;
        });
    }

    image2x() {
        const replaceSiblingsSourceElements = (currentElement) => {
            currentElement.parentNode.childNodes.forEach((item) => {
                if (!item.tagName) {
                    return;
                }
                if (item.tagName.toLowerCase() !== 'source') {
                    return;
                }
                item.setAttribute('srcset', this.replaceImgTo2Img(item.getAttribute('srcset')))
            });
        };

        const pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;

        if (pixelRatio > 1) {
            this.handleBgImageTo2xBg();

            const els = document.querySelectorAll('img.image2x');

            for (let i = 0; i < els.length; i++) {
                const currentElement = els[i];

                replaceSiblingsSourceElements(currentElement);

                const src = this.replaceImgTo2Img(currentElement.getAttribute('src'));
                if (this.useIfFileExists && !this.urlExists(src)) {
                    return;
                }

                currentElement.setAttribute('src', src);
                currentElement.setAttribute('srcset', this.replaceImgTo2Img(currentElement.getAttribute('srcset')));
                currentElement.setAttribute('data-srcset', this.replaceImgTo2Img(currentElement.getAttribute('data-srcset')));
                currentElement.setAttribute('data-src', this.replaceImgTo2Img(currentElement.getAttribute('data-src')));
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const o = new InitLozad();
    o.init();
});