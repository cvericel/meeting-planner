(function (window, $, swal) {
    /* Private class */
    let HelperInstance = new WeakMap();

    class MeetingDate {
        constructor($wrapper, $datepicker, $timepicker) {
            this.$wrapper = $wrapper;
            this.$datepicker = $datepicker;
            this.$timepicker = $timepicker;
            HelperInstance.set(this, new Helper(this.$wrapper));

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

            //Add jQuery datepicker
            this.$datepicker.datetimepicker({
                timepicker:false,
                format:'Y-m-d',
                inline: true
            });

            //Add jQuery timepicker
            this.$timepicker.datetimepicker({
                datepicker: false,
                format: 'H:i',
                inline: true
            });
        }

        updateNumberOfMeetingDate() {
            this.$wrapper.find('.js-number-of-meeting').html(
                HelperInstance.get(this).getNumberOfMeetingDateString()
            );
        }
        meetingDateAdd(e) {
            e.preventDefault();

            const $form = $(e.currentTarget);
            const $tbody = this.$wrapper.find('tbody.js-tbody-delete-meeting-date');
            const $modalForm = this.$wrapper.find('.js-modal-meeting-date-form');

            const url = $form.attr('action');

            $.ajax({
                url,
                method: 'POST',
                data: $form.serialize(),
            }).then(data => {
                $modalForm.modal('hide');
                $tbody.prepend(data);
                this.updateNumberOfMeetingDate();
                // reset datetimepicker
                this.$datepicker.datetimepicker('reset');
                this.$timepicker.datetimepicker('reset');
            }).catch(jqXHR => {
                $form.closest('.js-new-meeting-date-form-wrapper')
                    .html(jqXHR.responseText);
            });
        }

        meetingDateDelete(e) {
            e.preventDefault();

            const $target = $(e.currentTarget);
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: () => this._deleteMeetingDate($target)
            });
        }

        _deleteMeetingDate($target) {
            $target.find('.fa')
                .removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            const deleteUrl = $target.data('url');
            const $row = $target.closest('tr');

            //Delete meeting date with AJAX
            return $.ajax({
                url: deleteUrl,
                method: "DELETE"
            }).then(() => {
                $row.fadeOut('normal', () => {
                    $row.remove();
                    this.updateNumberOfMeetingDate();
                });
            });
        }
    }

    class Helper {
        constructor($wrapper) {
            this.$wrapper = $wrapper;
        }

        getNumberOfMeetingDateString(maxDate = 10) {
            let totalDate = this.calculateNumberOfMeetingDate();

            if (totalDate > maxDate) {
                totalDate = maxDate + '+';
            }

            return totalDate + ' date';
        }

        calculateNumberOfMeetingDate() {
            return Helper._calculateNumberOfMeetingDate(
                this.$wrapper.find('tbody.js-tbody-delete-meeting-date tr')
            );
        }

        static _calculateNumberOfMeetingDate($elements) {
            let numberMeetingDate = 0;

            $elements.each(() => {
                numberMeetingDate++;
            });

            return numberMeetingDate;
        }
    }

    window.MeetingDate = MeetingDate;
})(window, jQuery, swal);