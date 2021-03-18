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
                this.$valueContainer = $(e.currentTarget).siblings('.cell-editable-value');
                this.$errorMessagesContainer = $(e.currentTarget).siblings('.cell-editable__error-messages');

                if (this.$cancelLink !== null) {
                    this.$cancelLink.off('click').on('click', (e) => {
                        e.preventDefault();
                        this.$activeCell.removeClass('active');
                        this.$activeCell = null;
                    });
                }

                if (this.$saveLink !== null) {
                    this.$saveLink.off('click').on('click', (e) => {
                        e.preventDefault();
                        this.errorMessages = '';
                        if (this.$form !== null && typeof this.$form[0] === 'object') {
                            let url = this.$form.attr('action');
                            let formData = new FormData(this.$form[0]);
                            fetch(url, {
                                'method' : 'POST',
                                'body' : formData
                            })
                                .then(result => result.json())
                                .then(res => {
                                    if (typeof res.errors !== "undefined" && typeof res.data !== "undefined") {
                                        if (this.$valueContainer !== null && typeof  res.data === 'object') {
                                            let inputValue = this.$valueContainer.text();
                                            if($.isEmptyObject(res.errors)) {
                                                $.each(res.data, (k, v) => {if (!!v) {
                                                    this.$input = this.$form.find('[name*=' + k + ']');
                                                        this.$valueContainer.text(v);
                                                        inputValue = v;
                                                        let inputType = this.$input.prop('type');
                                                        switch (inputType) {
                                                            case 'textarea' :
                                                                this.$input.text(v);
                                                            break;
                                                            default:
                                                                this.$input.val(v);
                                                            break;
                                                        }
                                                    }
                                                });
                                            } else {
                                                $.each(res.errors, (k, v) => {
                                                    this.$input = this.$form.find('[name*=' + k + ']');
                                                    this.errorMessages += v + '<br>';
                                                });
                                                this.$errorMessagesContainer.html(this.errorMessages);
                                                this.errorMessages = '';
                                                this.$errorMessagesContainer.addClass('active');
                                            }
                                        }
                                    }
                                })
                                .catch(e => console.error(e))
                        }
                        this.clearActive();
                    });
                }
            });
        }
        $(document).off('click').on('click', (e) => {
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
    }
}

$(document).ready(() => {
    SideNav.init();
    FileInput.init();
    CellEditable.init();
});