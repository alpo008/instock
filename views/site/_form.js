const OperationForm = {
    $modal: {},
    modalForm: {},
    alertContainer: {},

    init () {
        this.$modal = $(document).find('#modalMaterialOperationForm');
        this.modalForm = document.getElementById('stock-operations-modal-form');
        this.alertContainer = document.getElementById('operation-alert');
        if (this.$modal.length) {
            this.$modal.modal('show');
        } else {
            let modalBackDrop = document.querySelector('.modal-backdrop');
            if (modalBackDrop !== null) {
                modalBackDrop.remove();
            }
        }
        if (this.modalForm !== null) {
            $(this.modalForm).off('submit').on('submit', (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();
                let url = this.modalForm.action;
                let method = this.modalForm.method;
                let formData = new FormData(this.modalForm);
                let message;
                let alertClass = 'alert-danger';
                fetch(url, {
                    'method' : method,
                    'body': formData,
                }).then(response => response.json())
                    .then(res => {
                        if (res.status === 200) {
                            alertClass = 'alert-success';
                        }
                        message = res.message;
                    })
                    .catch(error => console.error(error));
                this.$modal.modal("hide");
                $.pjax.reload("#site-index-pjax-container");
                setTimeout(() => {this.showAlert(alertClass, message)}, 400);
                OperationForm.init();

            });
        }
    },

    showAlert (alertClass, message)
    {
        if (this.alertContainer !== null) {
            let messageContainer = document.getElementById('alert-message');
            if (messageContainer !== null) {
                messageContainer.innerText = message;
                this.alertContainer.classList.remove('alert-danger', 'alert-success');
                this.alertContainer.classList.add(alertClass);
                this.alertContainer.style.display = 'block';
            }
        }
    }

}

$(document).ready(() => {
    OperationForm.init();
});
$(document).on('pjax:success', function() {
    OperationForm.init();
})
