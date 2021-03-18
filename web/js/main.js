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
    $form: null,

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
                        if (this.$form !== null && typeof this.$form[0] === 'object') {
                            let url = this.$form.attr('action');
                            let formData = new FormData(this.$form[0]);
                            fetch(url, {
                                'method' : 'POST',
                                'body' : formData
                            })
                                .then(result => result.json())
                                .then(res => {
                                    if (typeof res.error !== "undefined") {
                                        if (res.error === false) {
                                            if (this.$valueContainer !== null && typeof  res.data === 'object') {
                                                let newValue = this.$valueContainer.val();
                                                $.each(res.data, (k, v) => {
                                                    if (!!v) {
                                                        newValue = v;
                                                    }
                                                });
                                                this.$valueContainer.text(newValue);
                                            }
                                        }
                                    }
                                    alert('SUPER');
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