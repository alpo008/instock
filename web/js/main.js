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

$(document).ready(() => {
    SideNav.init();
    FileInput.init();
});