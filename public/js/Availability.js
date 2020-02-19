(function (window, $, swal) {
    window.Availability = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-meeting-date-choice',
            this.meetingDateChoice.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-meeting-date-choice-cancel',
            this.availabilityCancel.bind(this)
        );
    };
    $.extend(Availability.prototype, {
        meetingDateChoice: function(e){
            e.preventDefault();

            let $target = $(e.currentTarget);
            let url = $target.data('url');
            this.__ajaxRequest(url, $target);

        },
        availabilityCancel: function (e) {
            e.preventDefault();

            let $target = $(e.currentTarget);
            let url = $target.data('url');

            this.__ajaxRequest(url, $target);

        },
        __ajaxRequest: function (url, $target) {
            $.ajax({
                url: url,
                method: 'post'
            }).then(function (data) {
                let $availabilityWrapper = $target.closest('.js-availability-wrapper');
                $availabilityWrapper.html(data);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 30000,
                    timerProgressBar: true,
                    onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }});

                Toast.fire({
                    icon: 'success',
                    title: 'Choix enregistré !'
                })
            }).catch(function (jqXHR) {
                console.log("Ajax error");
            });
        }
    })
})(window, jQuery, swal);