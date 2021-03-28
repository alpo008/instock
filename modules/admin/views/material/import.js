const MaterialImport = {
    form: {},
    progress: {},
    progressBar: {},
    url: '',
    method: '',
    csrf: '',
    init () {
        this.form = document.querySelector('.modal-form');
        this.progress = document.querySelector('.progress');
        this.progressBar = this.progress.querySelector('.progress-bar');
        let csrfInput = this.form.querySelector('[name="_csrf"]');
        if (csrfInput !== null) {
            this.csrf = csrfInput.value;
        }
        if (this.form !== null && this.progress !== null && this.progressBar !== null && this.csrf) {
            this.modal = this.form.closest('.modal');
            this.url = this.form.action;
            this.method = this.form.method;
            if (this.modal !== null && !!this.url && !!this.method) {
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    this.toggleElement(this.form, 'none');
                    this.toggleElement(this.progress, 'flex');
                    let buttonClose = this.modal.querySelector('button.close');
                    if (buttonClose !== null) {
                        this.toggleElement(buttonClose, 'none');
                    }
                    let formData = new FormData(this.form);
                    this.request(formData);
                });
            }
        }
    },

    request (body) {
        fetch(this.url, {
            'method' : this.method,
            'body': body,
        }).then(result => result.json())
            .then(res => {
                if (res.error === false) {
                    if (typeof res.total !== 'undefined' && typeof res.processed !== 'undefined' ) {
                        this.showProgress(res.processed, res.total);
                        if (res.processed < res.total) {
                            let formData = new FormData();
                            formData.append('_csrf', this.csrf);
                            formData.append('startRow', res.processed + 1);
                            formData.append('endRow', res.processed + 50);
                            this.request(formData);
                        } else {
                            document.location.reload();
                        }
                    }
                }
            })
            .catch(error => console.error(error));
    },

    toggleElement (element, display) {
            element.style.display = display;
            element.style.display = display;
    },

    showProgress (processed, total) {
        let percentage = 0;
        if (!isNaN(processed) && !isNaN(total) && total !== 0) {
            percentage = Math.floor(100 * processed / total);
            if (!isNaN(percentage)) {
                let percentageString = percentage + '%';
                this.progressBar.setAttribute('aria-valuenow', percentage);
                this.progressBar.style.width = percentageString;
                this.progressBar.innerText = percentageString;
            }
        }
        return percentage;
    }
}

$(document).ready((e) => {
    MaterialImport.init();
});