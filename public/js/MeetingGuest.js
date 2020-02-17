(function (window, $, swal) {
    window.MeetingGuest = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            '.js-add-meeting-guest',
            this.meetingAddGuest.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-meeting-guest',
            this.meetingDeleteGuest.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-update-meeting-guest',
            this.updateMeetingGuest.bind(this)
        );
    };

    $.extend(MeetingGuest.prototype, {
        meetingAddGuest: function(e) {
            e.preventDefault();
            let self = this;
            let $guestForm = $('.js-add-meeting-guest');
            let $tbody = self.$wrapper.find('tbody.js-tbody-delete-meeting-guest');
            $.ajax({
                url: $guestForm.attr('action'),
                method: 'POST',
                data: $guestForm.serialize()
            }).then(function (data) {
                $tbody.prepend(data);
            }).catch(function (jqXHR) {
                //On supprime la span s'il y'a des une erreur
                if ($guestForm.hasClass("has-error")) {
                    $guestForm.find(".js-span-error").remove();
                }
                $guestForm.addClass("has-error");
                let span = "<div class='js-span-error text-danger'>"+ jqXHR.responseText + "</div>";
                $guestForm.append(span);
            });
        },
        meetingDeleteGuest: function(e) {
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
                    return self._deleteMeetingGuest($target);
                }
            })
        },
        _deleteMeetingGuest: function ($target) {
            $target.find('.fa')
                .removeClass('fa-times')
                .addClass('fa-spinner')
                .addClass('fa-spin');
            let deleteUrl = $target.data('url');
            let $row = $target.closest('tr');
            let self = this;

            //Delete meeting user with AJAX
            return $.ajax({
                url: deleteUrl,
                method: "DELETE"
            }).then(function () {
                $row.fadeOut('normal', function () {
                    $row.remove();
                })
            })
        },
        updateMeetingGuest: function (e) {
            e.preventDefault();
            let $target = $(e.currentTarget);
            let self = this;
            swal.fire({
                title: 'Are you sure?',
                text: "You will be able to revert this",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, upgrade it!',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return self._updateMeetingGuest($target);
                }
            })
        },
        _updateMeetingGuest: function ($target) {
            $target.find('.fa')
                .removeClass('fa-arrow-up')
                .addClass('fa-spinner')
                .addClass('fa-spin');
            let upgradeUrl = $target.data('url');
            let self = this;
            $.ajax({
                url: upgradeUrl,
                method: 'POST'
            }).then(function (data) {
                $td = $target.closest('.js-update-meeting-guest-td');
                console.log($td);
                $td.html(data);
            }).catch(function (jqXHR) {
                console.log(jqXHR.responseText);
            })
        }
    });
})(window, jQuery, swal);