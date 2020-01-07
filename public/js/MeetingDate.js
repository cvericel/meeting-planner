(function (window, $) {

    window.MeetingDate = function ($wrapper) {
        this.$wrapper = $wrapper;
        this.helper = new Helper(this.$wrapper);

        this.$wrapper.on(
            'click',
            '.js-delete-meeting-date',
            this.meetingDateDelete.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.js-new-meeting-date-form',
            this.meetingDateAdd.bind(this)
        )
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
            let $tbody = this.$wrapper.find('tbody');
            let self = this;

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                error: function (jqXHR){
                    $form.closest('.js-new-meeting-date-form-wrapper')
                        .html(jqXHR.responseText);

                }
            }).done(function (data) {
                $tbody.append(data);
                self.updateNumberOfMeetingDate();
            });
        },
        meetingDateDelete: function (e) {
            e.preventDefault();

            let $target = $(e.currentTarget);
            $target.find('.fa')
                .removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            let deleteUrl = $target.data('url');
            let $row = $target.closest('tr');
            let self = this;

            //Supprime une date de réunion avec de l'ajax
            $.ajax({
                url: deleteUrl,
                method: "DELETE",
            }).done(function () {
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
            this.$wrapper.find('tbody tr').each(function () {
                numberMeetingDate++;
            });

            return numberMeetingDate;
        }
    });
})(window, jQuery);