// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Javascript controller for the "Actions" panel at the bottom of the page.
 *
 * @module     local_courseteaser_admin/sortorder
 * @copyright  2021 Brain station 23 <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/notification', 'core/ajax', 'core/str'], function ($, notification, ajax) {

    function render_indivisual_rating(courseid) {
        var wsfunction = 'get_indivisual_rating';
        var params = {
            'cmid': courseid
        };

        var request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function (data) {
                if (data.success == true) {
                    $('#five-star').html(data.rated5);
                    $('#four-star').html(data.rated4);
                    $('#three-star').html(data.rated3);
                    $('#two-star').html(data.rated2);
                    $('#one-star').html(data.rated1);

                } else {
                    notification.addNotification({
                        message: data.result,
                        type: 'error'
                    });
                }
            }).fail(notification.exception);
        } catch (e) {
        }

    }

    function render_ratings(courseid) {
        var wsfunction = 'get_all_ratings';
        var params = {
            'cmid': courseid
        };

        var request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function (data) {
                if (data.success == true) {
                    var htm = '';
                    if (data.ratings.length > 0) {
                        data.ratings.forEach((item) => {
                            htm += '<div class="middle-last-part">' +
                                '                            <div class="person-rating">' +
                                '                                <div class="person">' +item.profilepicture +
                                '                                </div>' +
                                '                                <p class="person-name">' + item.firstname + ' ' + item.lastname + '</p>' +
                                '                                <span class="date">' + item.ratingdate + '</span>' +
                                '                            </div>' +
                                '                            <div class="person-comment">' +
                                '                                <p class="comment-of-person"> ' + item.comment +
                                '                                </p>' +
                                '                                <div class="star-rate">' +
                                '                                    <h5 class="star-head"> ' + generate_rating_star(item.rating) +
                                '                                    </h5>' +
                                '                                </div>' +
                                '                            </div>' +
                                '                        </div>';
                        });
                        $('#total-rating').html(data.ratings.length);
                    }
                    else {
                        $('#total-rating').html('0');
                        htm += 'No Rating Found';
                    }
                    $('#user-ratings').html(htm);

                } else {
                    notification.addNotification({
                        message: data.result,
                        type: 'error'
                    });
                }
            }).fail(notification.exception);
        } catch (e) {
        }

    }

    function generate_rating_star(stars) {
        var htm = '';
        for (var i = 0; i < stars; i++) {
            htm += '<i class="far fa-star star-icon-rate" aria-hidden="true"></i>';
        }
        return htm;
    }

    function render_avg_rating(courseid) {
        var wsfunction = 'get_cm_rating';
        var params = {
            'cmid': courseid
        };

        var request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function (data) {
                if (data.success == true) {
                    var htm = '';
                    if (data.rating) {
                        var rat = Math.round(data.rating).toFixed(1);
                        for (var i = 0; i < Math.round(data.rating); i++) {
                            htm += '<i class="far fa-star star-icon"></i>';
                        }
                    } else {
                        var rat = '0.0';
                        htm += '<i class="star-icon">No rating found</i>';
                    }
                    $('#avg_rating').html(rat);
                    $('#avg_ret_star').html(htm);
                } else {
                    notification.addNotification({
                        message: data.result,
                        type: 'error'
                    });
                }
            }).fail(notification.exception);
        } catch (e) {
        }

    }

    return {
        init: function (courseid, uid) {
            render_avg_rating(courseid);
            render_indivisual_rating(courseid);
            render_ratings(courseid);
            $(document).on('click', '#submit_rating_form', function () {
                var rating = $('input[name="star"]:checked').val();
                var comment = $('#comment').val();
                if (rating == undefined) {
                    notification.addNotification({
                        message: 'rating field is required',
                        type: 'error'
                    });
                    return false;
                }

                // API Call
                var wsfunction = 'save_rating';
                var params = {
                    'userid': uid,
                    'cmid': courseid,
                    'rating': rating,
                    'comment': comment,
                };

                var request = {
                    methodname: wsfunction,
                    args: params
                };

                try {
                    ajax.call([request])[0].done(function (data) {
                        if (data.success == 1) {
                            $('input[name="star"]').prop('checked', false);
                            $('#rating-form').addClass('d-none');
                            render_avg_rating(courseid);
                            render_indivisual_rating(courseid);
                            render_ratings(courseid);
                            notification.addNotification({
                                message: data.result,
                                type: 'success'
                            });

                        } else {
                            notification.addNotification({
                                message: data.result,
                                type: 'error'
                            });
                        }
                    }).fail(notification.exception);
                } catch (e) {
                }

            });
        }
    };
});