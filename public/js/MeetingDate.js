(function (window, $, swal) {
    window.MeetingDate = function ($wrapper, $datetimepicker) {
        this.$wrapper = $wrapper;
        this.$datetimepicker = $datetimepicker;
        this.helper = new Helper(this.$wrapper);

        //Delete meeting date event listener
        this.$wrapper.on(
            'click',
            '.js-delete-meeting-date',
            this.meetingDateDelete.bind(this)
        );

        //Add meeting date event listener
        this.$wrapper.on(
            'submit',
            '.js-new-meeting-date-form',
            this.meetingDateAdd.bind(this)
        );

        //Add jQuery datetimepicker
        $.datetimepicker.setLocale('fr');
        this.$datetimepicker.datetimepicker({
            lang: 'fr',
            step: 5,
            inline: true,
            format: 'Y-m-d H:m:s'
        });
    };

    $.extend(MeetingDate.prototype, {
        updateNumberOfMeetingDate: function () {
            this.$wrapper.find('.js-number-of-meeting').html(
                this.helper.calculateNumberOfMeetingDate()
            );
        },
        meetingDateAdd: function (e) {
            e.preventDefault();

            let $form = $(e.currentTarget);
            let $tbody = this.$wrapper.find('tbody.js-tbody-delete-meeting-date');
            let $modalForm = this.$wrapper.find('.js-modal-meeting-date-form');
            let self = this;

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
            }).then(function (data) {
                $modalForm.modal('hide');
                $tbody.prepend(data);
                self.updateNumberOfMeetingDate();
                self.$datetimepicker.datetimepicker('reset');
            }).catch(function (jqXHR) {
                $form.closest('.js-new-meeting-date-form-wrapper')
                    .html(jqXHR.responseText);
            });
        },
        meetingDateDelete: function (e) {
            e.preventDefault();

            let $target = $(e.currentTarget);
            let self = this;
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return self._deleteMeetingDate($target);
                }
            }).then((result) => {

            })
        },
        _deleteMeetingDate: function ($target) {
            $target.find('.fa')
                .removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            let deleteUrl = $target.data('url');
            let $row = $target.closest('tr');
            let self = this;

            //Delete meeting date with AJAX
            return $.ajax({
                url: deleteUrl,
                method: "DELETE"
            }).then(function () {
                $row.fadeOut('normal', function () {
                    $row.remove();
                    self.updateNumberOfMeetingDate();
                });
            });
        }
    });


    /**
     * A "private" object
     *
     */
    let Helper = function ($wrapper) {
        this.$wrapper = $wrapper;
    };
    $.extend(Helper.prototype, {
        calculateNumberOfMeetingDate: function () {
            let numberMeetingDate = 0;
            this.$wrapper.find('tbody.js-tbody-delete-meeting-date tr').each(function () {
                numberMeetingDate++;
            });

            return numberMeetingDate;
        }
    });
})(window, jQuery, swal);