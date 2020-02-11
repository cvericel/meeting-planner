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
            console.log("2" + url);
            $.ajax({
                url: url,
                method: 'post'
            }).then(function (data) {
                let $availabilityWrapper = $target.closest('.js-availability-wrapper');
                $availabilityWrapper.html(data);
            }).catch(function (jqXHR) {
                console.log("Ajax error");
            });
        }
    })
})(window, jQuery, swal);