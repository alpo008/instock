'use strict'
const SideNav = {
    $sidebar: {},
    $switch: {},
    init() {
        this.$sidebar = $(document).find('#sidebar');
        this.$switch = $(document).find('#sidebarCollapse');
        if (typeof this.$sidebar === 'object') {
            this.$switch.on('click',(e) => {
                this.$sidebar.toggleClass('active');
                this.$switch.toggleClass('navbar-collapsed');
            });
        }
    }
};

$(document).ready(() => {
    SideNav.init();
});