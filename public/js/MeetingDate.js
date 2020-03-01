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

        //a faire
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
                console.log(this.$carousel);
                this.$carousel
                    .trigger('add.owl.carousel', data, 0)
                    .trigger('refresh.owl.carousel');
                console.log(this.$carousel);

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
            const $item = $target.closest('.owl-item');

            //Delete meeting date with AJAX
            return $.ajax({
                url: deleteUrl,
                method: "DELETE"
            }).then(() => {

                this.updateNumberOfMeetingDate();
                this.$carousel.trigger('remove.owl.carousel', $item.index());
                this.$carousel.trigger('refresh.owl.carousel')

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