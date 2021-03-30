'use strict'
const SideNav = {
    $sidebar: {},
    $switch: {},
    init() {
        this.$sidebar = $(document).find('#sidebar');
        this.$switch = $(document).find('#sidebarCollapse');
        if (typeof this.$sidebar === 'object') {
            this.$switch.on('click',(e) => {
                e.preventDefault();
                this.$sidebar.toggleClass('active');
                this.$switch.toggleClass('navbar-collapsed');
            });
        }
    }
};

const FileInput = {
    $input: {},
    $previewContainer: {},
    reader: null,
    init() {
        this.$input = $(document).find(':file');
        this.$previewContainer = $(document).find('img.file-upload');
        this.reader = new FileReader();
        if (this.$input.length === 1 && this.$previewContainer.length === 1) {
            this.$previewContainer.off('click').on('click', (e) => {
                this.$input.trigger('click');
            });
            this.$input.off('change').on('change', (e) => {
                let files = e.currentTarget.files;
                if (typeof files[0] === 'object') {
                    this.reader.onload = (e) => {
                        this.$previewContainer.attr('src', e.target.result);
                    };
                    this.reader.readAsDataURL(files[0]);
                }
            });
        }
    }
}

const CellEditable = {
    $editLink: null,
    $activeCell: null,
    $cancelLink: null,
    $saveLink: null,
    $valueContainer: null,
    errorMessages: '',
    $errorMessagesContainer: null,
    $form: null,
    $input: null,
    oldValue: null,

    init () {
        this.$editLink = $(document).find('.cell-editable__icon_edit');
        if (this.$editLink !== null) {
            this.$editLink.off('click').on('click', (e) => {
                e.preventDefault();
                this.clearActive();
                this.$activeCell = $(e.currentTarget).closest('.cell-editable');
                this.$activeCell.addClass('active');
                this.$cancelLink = $(e.currentTarget).siblings('.cell-editable__icon_cancel');
                this.$saveLink = $(e.currentTarget).siblings('.cell-editable__icon_save');
                this.$form = $(e.currentTarget).siblings('form');
                this.$input = this.$form.find('.cell-editable__input');
                this.$valueContainer = $(e.currentTarget).siblings('.cell-editable-value');
                this.$errorMessagesContainer = $(e.currentTarget).siblings('.cell-editable__error-messages');
                this.oldValue = this.$valueContainer.text();

                if (this.$cancelLink !== null) {
                    this.$cancelLink.off('click').on('click', (e) => {
                        e.preventDefault();
                        this.$activeCell.removeClass('active');
                        this.$activeCell = null;
                    });
                }

                if (this.$saveLink !== null && this.$form !== null) {
                    this.$saveLink.off('click').on('click', (e) => {
                        e.preventDefault();
                        this.$form.trigger('submit');
                    });
                }

                if (this.$form !== null && typeof this.$form[0] === 'object') {
                    this.$form.off('submit').on('submit', (e) => {
                        e.preventDefault();
                        this.errorMessages = '';
                        let url = this.$form.attr('action');
                        let formData = new FormData(this.$form[0]);
                        fetch(url, {
                            'method' : 'POST',
                            'body' : formData
                        })
                            .then(result => result.json())
                            .then(res => {
                                if (typeof res.errors !== "undefined" && typeof res.newValue !== "undefined") {
                                    if (!!res.newValue.length && !res.errors.length) {
                                        this.$valueContainer.text(res.newValue);
                                    } else {
                                        if (typeof res.errors === 'object' && !$.isEmptyObject(res.errors)) {
                                            this.updateInput(this.oldValue);
                                            this.showErrors(res.errors);
                                        }
                                    }
                                }
                            })
                            .catch(e => console.error(e));
                        this.clearActive();
                    });
                }
            });
        }
        $(document).on('click', (e) => {
            if ($(e.target).closest('.cell-editable.active').length === 0) {
                this.clearActive();
            }
            $(document).find('.cell-editable__error-messages.active').removeClass('active');
        })
    },

    clearActive () {
        let $activeCells = $(document).find('.cell-editable.active');
        if (!!$activeCells.length) {
            $activeCells.each((i, elem) => {
                $(elem).removeClass('active');
            });
        }
        this.$activeCell = null;
    },

    updateInput (val) {
        if (this.$input !== null) {
            let inputType = this.$input.prop('type');
            switch (inputType) {
                case 'textarea' :
                    this.$input.text(val);
                break;
                default:
                    this.$input.val(val);
                break;
            }
        }
    },

    showErrors (errors) {
        if (this.$errorMessagesContainer !== null && typeof errors === 'object') {
            $.each(errors, (k, v) => {
                this.errorMessages += v + '<br>';
            });
            this.$errorMessagesContainer.html(this.errorMessages);
            this.errorMessages = '';
            this.$errorMessagesContainer.addClass('active');
        }

    }
}

const MaterialImport = {
    form: {},
    progress: {},
    progressBar: {},
    url: '',
    method: '',
    csrf: '',
    touched: 0,
    init () {
        this.form = document.querySelector('.modal-import-form');
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
                    let infoBlock = this.modal.querySelector('.info-block');
                    if (infoBlock !== null) {
                        this.toggleElement(infoBlock, 'none');
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
                        if (typeof res.touched === 'number') {
                            this.touched += res.touched
                        }
                        if (res.processed < res.total) {
                            let formData = new FormData();
                            formData.append('_csrf', this.csrf);
                            formData.append('startRow', res.processed + 1);
                            formData.append('endRow', res.processed + 50);
                            this.request(formData);
                        } else {
                            this.showSuccess(res.processed, this.touched);
                        }
                    }
                } else {
                    if (typeof res.error === 'string') {
                        this.showError(res.error);
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
    },

    showSuccess (processed, touched) {
        let message = 'Импорт выполнен'
        if (!isNaN(processed && !isNaN(touched))) {
            message = 'Обработано строк : ' + processed + ', изменено записей в БД : ' + touched + '.';
        }
        let alertSuccess = document.querySelector('.alert-success');
        if (typeof alertSuccess === 'object') {
            alertSuccess.innerHTML = message;
            this.toggleElement(alertSuccess, 'block');
        }
        this.toggleElement(this.progress, 'none');
        let modalFooter = document.querySelector('.modal-footer');
        if (typeof modalFooter === 'object') {
            this.toggleElement(modalFooter, 'flex');
        } else {
            setTimeout(() => {
                document.location.reload();
            }, 2000);
        }
    },

    showError(error) {
        let message = !!error.length ? error : 'Ошибка импорта';
        let alertDanger = document.querySelector('.alert-danger');
        this.toggleElement(this.progress, 'none');
        if (typeof alertDanger === 'object') {
            alertDanger.innerHTML = message;
            this.toggleElement(alertDanger, 'block');
        }
        let buttonClose = this.modal.querySelector('button.close');
        this.toggleElement(buttonClose, 'flex');
    }
}

$(document).ready(() => {
    SideNav.init();
    FileInput.init();
    CellEditable.init();
    MaterialImport.init();
});