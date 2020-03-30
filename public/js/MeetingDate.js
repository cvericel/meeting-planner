(function (window, $, swal, owlcarousel) {
    /* Private class */
    let HelperInstance = new WeakMap();

    class MeetingDate {
        constructor($wrapper, $datepicker, $timepicker, $carousel) {
            this.$wrapper = $wrapper;
            this.$datepicker = $datepicker;
            this.$timepicker = $timepicker;
            this.$carousel = $carousel;

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

            this.$wrapper.on(
              'click',
              '.js-meeting-date-view-availability',
              this.meetingDateViewAvailabity.bind(this)
            );

            this.$wrapper.on(
                'click',
                '.js-choose-meeting-date',
                this.meetingDateChoose.bind(this)
            );

            //Add jQuery datepicker
            this.$datepicker.datetimepicker({
                timepicker:false,
                format:'Y-m-d',
                inline: true,
                minDate: Date
            });

            //Add jQuery timepicker
            this.$timepicker.datetimepicker({
                datepicker: false,
                format: 'H:i',
                inline: true,
                allowTimes: [
                    '08:00', '08:05', '08:10', '08:15', '08:20', '08:25', '08:30', '08:35', '08:40', '08:45', '08:50', '08:55',
                    '09:00', '09:05', '09:10', '09:15', '09:20', '09:25', '09:30', '09:35', '09:40', '09:45', '09:50', '09:55',
                    '10:00', '10:05', '10:10', '10:15', '10:20', '10:25', '10:30', '10:35', '10:40', '10:45', '10:50', '10:55',
                    '11:00', '11:05', '11:10', '11:15', '11:20', '11:25', '11:30', '11:35', '11:40', '11:45', '11:50', '11:55',
                    '12:00', '12:05', '12:10', '12:15', '12:20', '12:25', '12:30', '12:35', '12:40', '12:45', '12:50', '12:55',
                    '13:00', '13:05', '13:10', '13:15', '13:20', '13:25', '13:30', '13:35', '13:40', '13:45', '13:50', '13:55',
                    '14:00', '14:05', '14:10', '14:15', '14:20', '14:25', '14:30', '14:35', '14:40', '14:45', '14:50', '14:55',
                    '15:00', '15:05', '15:10', '15:15', '15:20', '15:25', '15:30', '15:35', '15:40', '15:45', '15:50', '14:55',
                    '16:00', '16:05', '16:10', '16:15', '16:20', '16:25', '16:30', '16:35', '16:40', '16:45', '16:50', '16:55',
                    '17:00', '17:05', '17:10', '17:15', '17:20', '17:25', '17:30', '17:35', '17:40', '17:45', '17:50', '17:55',
                    '18:00', '18:05', '18:10', '18:15', '18:20', '18:25', '18:30', '18:35', '18:40', '18:45', '18:50', '18:55',
                ],
            });

            this.$carousel.owlCarousel({
                margin:20,
                autoWidth: true,
                responsiveClass:true,
                responsive : {
                    0 : {
                        items : 1
                    },
                    480 : {
                        items : 1,
                        center: true
                    },
                    768 : {
                        items : 1,
                    }
                },
                loop:false,
                autoHeight: true,
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
            const $modalForm = this.$wrapper.find('.js-modal-meeting-date-form');
            const url = $form.attr('action');

            $.ajax({
                url,
                method: 'POST',
                data: $form.serialize(),
            }).then(data => {
                $modalForm.modal('hide');
                this.$carousel
                    .trigger('add.owl.carousel', data, 0)
                    .trigger('refresh.owl.carousel');

                // reset datetimepicker
                this.$datepicker.datetimepicker('reset');
                this.$timepicker.datetimepicker('reset');
            }).catch(jqXHR => {
                const id = this.$wrapper.data('url');
                window.location.replace("/admin/meeting/" + id)
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
            const $item = $target.closest('.owl-item');

            //Delete meeting date with AJAX
            return $.ajax({
                url: deleteUrl,
                method: "DELETE"
            }).then(() => {
                this.updateNumberOfMeetingDate();
                this.$carousel.trigger('remove.owl.carousel', $item.index());
                this.$carousel.trigger('refresh.owl.carousel')
            }).catch(jqXHR => {
                Swal.fire({
                    title: '<strong>Success</strong>',
                    text: "You won't be able to revert this!",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            });
        }

        meetingDateViewAvailabity(e) {
            e.preventDefault();
            const $target = $(e.currentTarget);
            const url = $target.data('url');


            $.ajax({
                url: url,
                method: "POST"
            }).then((data) => {
                swal.fire({
                    title: '<strong>Availability list</strong>',
                    icon: 'info',
                    html: data,
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: '<i class="fa fa-thumbs-up"> </i> Great!',
                    confirmButtonAriaLabel: 'Thumbs up, great!',
                    cancelButtonText:'<i class="fa fa-thumbs-down"> </i>',
                    cancelButtonAriaLabel: 'Thumbs down'
                });
            })
        }

        meetingDateChoose(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, choose this date!'
            }).then((result) => {
                if (result.value) {
                    const $target = $(e.currentTarget);
                    const url = $target.data('url');

                    $.ajax({
                        url: url,
                        method: "POST"
                    }).then((data) => {
                        // update page
                        $.ajax({
                            url: "/meetings"
                        }).then((data) => {
                            let timerInterval;

                            Swal.fire({
                                title: 'Meeting date choose !',
                                icon: 'success',
                                html: 'People in meeting will receive confirmation email.',
                                timer: 3000,
                                timerProgressBar: true,
                                onBeforeOpen: () => {
                                    timerInterval = setInterval(() => {
                                        const content = Swal.getContent();
                                        if (content) {
                                            const b = content.querySelector('b');
                                            if (b) {
                                                b.textContent = Swal.getTimerLeft();
                                            }
                                        }
                                    }, 100)
                                },
                                onClose: () => {
                                    clearInterval(timerInterval);
                                    window.location.replace("/admin")
                                }
                            });
                        });
                    });
                }
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