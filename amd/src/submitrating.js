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
 * Schedule Setup Front End Activity Controller js.
 *
 * @author     BrainStation-23
 * @copyright  2022 Brain Station 23
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint linebreak-style: ["error", "windows"]*/

define([
    'jquery',
    'core/str',
    'core/notification',
    'core/ajax'], function($, str, notification, ajax) {

    /**
     * @param {int} courseid
     */
    function renderIndividualRating(courseid) {
        let wsfunction = 'get_indivisual_rating';
        let params = {
            'cmid': courseid
        };

        let request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function(data) {
                if (data.success == true) {
                    $('#five-star').html(data.rated5);
                    $('#four-star').html(data.rated4);
                    $('#three-star').html(data.rated3);
                    $('#two-star').html(data.rated2);
                    $('#one-star').html(data.rated1);

                } else {
                    // Notification.addNotification({
                    //     message: data.result,
                    //     type: 'error'
                    // });
                }
            }).fail(notification.exception);
        } catch (e) {
        }

    }

    /**
     * @param {int} courseid
     */
    function renderRatings(courseid) {
        let wsfunction = 'get_all_ratings';
        let params = {
            'cmid': courseid
        };

        let request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function(data) {
                if (data.success == true) {
                    let htm = '';

                    if (data.ratings.length > 0) {

                        data.ratings.forEach((item) => {
                            let today = new Date(item.ratingdate);
                            htm += '<div class="middle-last-part">' +
                                '<div class="person-rating">' +
                                '    <div class="person">' + item.profilepicture +
                                '    </div>' +
                                '    <p class="person-name">' + item.firstname + ' ' + item.lastname + '</p>' +
                                '    <span class="date">' + today.toLocaleString() + '</span>' +
                                '</div>' +
                                '<div class="person-comment">' +
                                '    <p class="comment-of-person"> ' + item.comment +
                                '    </p>' +
                                '    <div class="star-rate">' +
                                '        <h5 class=""> ' + generateRatingStar(item.rating) +
                                '        </h5>' +
                                '    </div>' +
                                '</div>' +
                                '</div>';
                        });
                        $('#total-rating').html(data.ratings.length);
                    } else {
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

    /**
     * @param {int} courseid
     */
    function renderAvgRating(courseid) {
        let wsfunction = 'get_cm_rating';
        let params = {
            'cmid': courseid
        };

        let request = {
            methodname: wsfunction,
            args: params
        };

        try {
            ajax.call([request])[0].done(function(data) {
                if (data.success == true) {
                    let htm = '';
                    let rat = '';
                    if (data.rating) {
                        rat = Math.round(data.rating).toFixed(1);
                        for (let i = 0; i < Math.round(data.rating); i++) {
                            // eslint-disable-next-line max-len
                            htm += '<svg width="45" height="40" viewBox="0 0 41 38" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                // eslint-disable-next-line max-len
                                '<path d="M7.16533 36.1269C6.97083 37.2355 8.06474 38.1033 9.03084 37.6068L20.0039 31.9669L30.977 37.6068C31.9431 38.1033 33.037 37.2355 32.8425 36.1269L30.7682 24.3032L39.574 15.9116C40.3969 15.1273 39.971 13.6934 38.8685 13.5368L26.6223 11.7971L21.162 0.980762C20.6701 0.00641251 19.3377 0.00641251 18.8458 0.980762L13.3855 11.7971L1.13934 13.5368C0.036863 13.6934 -0.389137 15.1273 0.433814 15.9116L9.23964 24.3032L7.16533 36.1269ZM19.4268 29.2065L10.2125 33.9425L11.9481 24.0493C12.0299 23.5829 11.8761 23.1053 11.5412 22.7863L4.27413 15.861L14.4049 14.4218C14.8237 14.3623 15.1894 14.0949 15.3869 13.7035L20.0039 4.55769L24.6209 13.7035C24.8185 14.0949 25.1841 14.3623 25.6029 14.4218L35.7337 15.861L28.4666 22.7863C28.1317 23.1053 27.9779 23.5829 28.0597 24.0493L29.7954 33.9425L20.581 29.2065C20.217 29.0195 19.7908 29.0195 19.4268 29.2065Z" fill="#9CA4B6"/>' +
                                '</svg>';
                        }
                    } else {
                        rat = '0.0';
                        htm += '<i class="star-icon">No rating found</i>';
                    }
                    $('#avg_rating').html(rat);
                    $('#avg-course-ret').html(generateRatingStar(rat));
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

    /**
     * @param  {int} stars
     */
    function generateRatingStar(stars) {
        let htm = '';
        for (let i = 0; i < stars; i++) {
            htm += '<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                // eslint-disable-next-line max-len
                '<path d="M3.58266 18.5634C3.48541 19.1178 4.03237 19.5517 4.51542 19.3034L10.002 16.4835L15.4885 19.3034C15.9715 19.5517 16.5185 19.1178 16.4212 18.5634L15.3841 12.6516L19.787 8.45578C20.1985 8.06366 19.9855 7.34671 19.4342 7.2684L13.3111 6.39856L10.581 0.990381C10.3351 0.503206 9.66885 0.503206 9.42291 0.990381L6.69276 6.39856L0.569668 7.2684C0.0184315 7.34671 -0.194569 8.06366 0.216907 8.45578L4.61982 12.6516L3.58266 18.5634ZM9.71342 15.1033L5.10623 17.4712L5.97405 12.5246C6.01495 12.2915 5.93803 12.0527 5.77061 11.8931L2.13706 8.4305L7.20245 7.71092C7.41184 7.68117 7.59468 7.54743 7.69346 7.35176L10.002 2.77884L12.3104 7.35176C12.4092 7.54743 12.5921 7.68117 12.8015 7.71092L17.8668 8.4305L14.2333 11.8931C14.0659 12.0527 13.989 12.2915 14.0299 12.5246L14.8977 17.4712L10.2905 15.1033C10.1085 15.0097 9.89541 15.0097 9.71342 15.1033Z" fill="#9CA4B6"/>' +
                '</svg>';
        }
        return htm;
    }

    return {
        init: function(courseid, uid) {
            renderAvgRating(courseid);
            renderIndividualRating(courseid);
            renderRatings(courseid);
            $("#submit_rating_form").on("click", function() {
                let rating = $('input[name="star"]:checked').val();
                let comment = $('#comment').val();
                if (rating == undefined) {
                    str.get_string('ratingfailed', 'local_rating_helper').then(function(langString) {
                        notification.addNotification({
                            message: langString,
                            type: 'error'
                        });

                    }).catch(Notification.exception);

                    return false;
                }

                // API Call
                let wsfunction = 'save_rating';
                let params = {
                    'userid': uid,
                    'cmid': courseid,
                    'rating': rating,
                    'comment': comment,
                };

                let request = {
                    methodname: wsfunction,
                    args: params
                };

                try {
                    ajax.call([request])[0].done(function(data) {
                        if (data.success == 1) {
                            $('input[name="star"]').prop('checked', false);
                            $('#rating-form').addClass('d-none');
                            renderAvgRating(courseid);
                            renderIndividualRating(courseid);
                            renderRatings(courseid);
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
        },
        copy: function() {
            $(".copy_url").on("click", function() {
                let dataUrl = $(this).attr("data-url");
                let $temp = $("<input>");
                $("body").append($temp);
                $temp.val(dataUrl).select();
                document.execCommand("copy", false, $temp.val());
                $temp.remove();
                notification.addNotification({
                    message: "Course Rating URL has been copied to clipboard.",
                    type: 'success'
                });
            });
        }
    };

});
