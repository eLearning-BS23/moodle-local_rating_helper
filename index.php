<?php
global $DB, $USER, $OUTPUT, $CFG, $PAGE;
require_once("../../config.php");
require_once($CFG->dirroot . '/local/rating_helper/lib.php');

require_login();

$courseid = required_param('course_id', PARAM_INT);
$course = $DB->get_record("course", array("id" => $courseid), "*", MUST_EXIST);
$sql = 'select * from {local_rating_helper} where cmid = ' . $courseid . ' AND userid=' . $USER->id;
$israting = $DB->get_record_sql($sql);
$courseurl = $CFG->wwwroot . '/course/view.php?id=' . $courseid;
$context = context_system::instance();
$PAGE->set_url('/rating_helper/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
$PAGE->set_heading(get_string('pluginname', 'local_rating_helper'));
$PAGE->navbar->add($course->fullname, $courseurl);
$PAGE->navbar->add(get_string('ratings', 'local_rating_helper'));
echo $OUTPUT->header();
$imageurl = get_course_image_url($courseid);
$paramsforratinguse = [
    'course_id' => $courseid,
    'user_id' => $USER->id,
];
$PAGE->requires->js_call_amd('local_rating_helper/submitrating', 'init', $paramsforratinguse);

?>


    <style>
        <?php
            include 'styles_local_rating_helper.css';
        ?>
    </style>

    <section class="container-fluid section-part">
<?php
$msg = html_writer::tag('h3', get_string('ratings', 'local_rating_helper'), ['class' => 'title-name']);
echo html_writer::tag('div', $msg, ['class' => 'ratings-title mb-4']);
?>
    <div class="row m-0">

        <div class="col-md-3 col-12 p-0 ">
            <div class="left-part">

                <div class="left-upper-part">
                    <?php
                    echo html_writer::tag('h1', '0.0', ['id' => "avg_rating", 'class' => 'five']);

                    $msg = html_writer::tag('h3', get_string('ratings', 'local_rating_helper'), ['id' => "avg_ret_star", 'class' => 'star-head']);
                    echo html_writer::tag('div', $msg, ['class' => 'star']);
                    ?>
                </div>
                <?php
                $a = get_string('basedon', 'local_rating_helper') . ' <b id="total-rating"></b> ' . get_string('ratings', 'local_rating_helper');
                echo html_writer::tag('p', $a, ['class' => 'based-on']);
                ?>
                <hr>
            </div>

            <div class="star-of-star">
                <div class="star">
                    <h5 class="star-head">

                        <?php
                        echo generate_star_dom(5);
                        ?>
                        <span class="five-star">| 5 Stars (<span id="five-star"></span>)</span>
                    </h5>

                </div>
                <div>
                    <div class="star">
                        <h5 class="star-head">
                            <?php
                            echo generate_star_dom(4);
                            ?>
                            <span class="four-star">| 4 Stars (<span id="four-star"></span>)</span>
                        </h5>

                    </div>
                    <div>
                        <div class="star">
                            <h5 class="star-head">
                                <?php
                                echo generate_star_dom(3);
                                ?>

                                <span class="three-star">| 3 Stars (<span id="three-star"></span>)</span>
                            </h5>

                        </div>
                        <div>
                            <div class="star">
                                <h5 class="star-head">
                                    <?php
                                    echo generate_star_dom(2);
                                    ?>


                                    <span class="two-star">| 2 Stars (<span id="two-star"></span>)</span>
                                </h5>

                            </div>
                            <div>
                                <div class="star">
                                    <h5 class="star-head">
                                        <?php
                                        echo generate_star_dom(1);
                                        ?>

                                        <span class="one-star">| 1 Stars (<span id="one-star"></span>)</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12 p-0 ">
            <div class="middle-part">

                <div class="experience-section">

                    <?php
                    if (!$israting) {
                        ?>
                        <div id="rating-form">
                            <?php
                            echo html_writer::tag('h4', get_string('whatyourfeedback', 'local_rating_helper'), ['class' => 'feedback mb-3']);

                            echo html_writer::tag('p', get_string('rateyourexperience', 'local_rating_helper'), ['class' => 'experience']);
                            ?>


                            <form method="post">

                                <div class="d-flex">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="stars">
                                                <input class="star star-5" required id="star-5" type="radio"
                                                       value="5"
                                                       name="star"/>
                                                <label class="star star-5" for="star-5"></label>
                                                <input class="star star-4" required id="star-4" type="radio"
                                                       value="4"
                                                       name="star"/>
                                                <label class="star star-4" for="star-4"></label>
                                                <input class="star star-3" required id="star-3" type="radio"
                                                       value="3"
                                                       name="star"/>
                                                <label class="star star-3" for="star-3"></label>
                                                <input class="star star-2" required id="star-2" type="radio"
                                                       value="2"
                                                       name="star"/>
                                                <label class="star star-2" for="star-2"></label>
                                                <input class="star star-1" required id="star-1" type="radio"
                                                       value="1"
                                                       name="star"/>
                                                <label class="star star-1" for="star-1"></label>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-section">
                                    <p class="review"><?= get_string('leaveareview', 'local_rating_helper') ?></p>
                                    <div class="comment-section">
                                <textarea name="comment" id="comment" class="custom-rounded p-3 w-100" rows="4"
                                          maxlength="200"
                                          placeholder="<?= get_string('commentplaceholder', 'local_rating_helper') ?>"></textarea>
                                    </div>
                                    <div class="text-right mt-4 mb-4">
                                        <button type="button" id="submit_rating_form"
                                                class="btn rating-btn"><?= get_string('sendrate', 'local_rating_helper') ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <?php
                    }
                    echo html_writer::tag('h4', get_string('previousratings', 'local_rating_helper'), ['class' => 'pre-rate']);
                    echo html_writer::tag('div', '', ['id' => 'user-ratings']);
                    ?>

                </div>


            </div>
        </div>

        <div class="col-md-3 col-12">
            <div class="card custom-rounded w-100" style="width: 18rem;">
                <div class="custom-rounded-top" style="overflow: hidden; max-height: 200px">
                    <img class="w-100 custom-rounded-top"
                         src="<?= $imageurl ?>"
                         alt="Card image cap">
                </div>
                <div class="card-body">

                    <h5 class="eye"><a href="<?= $courseurl ?>"><?php echo $course->fullname; ?> </a></h5>

                    <div class="suffer-from">
                        <span class="suffer"><?php echo html_entity_decode($course->summary); ?></span>
                    </div>
                    <div class="icon-last my-2  flex-wrap">
                        <div id="avg-course-ret"></div>
                        <div class="flex mt-1">
                            <svg class="ml-1" width="16" height="20" viewBox="0 0 16 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M0.5 2.5C0.5 1.11929 1.61929 0 3 0H13C14.3807 0 15.5 1.11929 15.5 2.5V19.375C15.5 19.6055 15.3731 19.8173 15.1699 19.926C14.9667 20.0348 14.7201 20.0229 14.5283 19.895L8 16.3762L1.47169 19.895C1.2799 20.0229 1.03331 20.0348 0.830089 19.926C0.626865 19.8173 0.5 19.6055 0.5 19.375V2.5ZM3 1.25C2.30964 1.25 1.75 1.80964 1.75 2.5V18.2072L7.65331 15.105C7.86325 14.965 8.13675 14.965 8.34669 15.105L14.25 18.2072V2.5C14.25 1.80964 13.6904 1.25 13 1.25H3Z"
                                      fill="#9CA4B6"/>
                            </svg>
                            <svg class="ml-1" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.875 1.25C15.8395 1.25 15 2.08947 15 3.125C15 4.16053 15.8395 5 16.875 5C17.9105 5 18.75 4.16053 18.75 3.125C18.75 2.08947 17.9105 1.25 16.875 1.25ZM13.75 3.125C13.75 1.39911 15.1491 0 16.875 0C18.6009 0 20 1.39911 20 3.125C20 4.85089 18.6009 6.25 16.875 6.25C15.9263 6.25 15.0764 5.82727 14.5033 5.15991L6.10623 9.06025C6.19964 9.35686 6.25 9.67255 6.25 10C6.25 10.3275 6.19964 10.6431 6.10623 10.9397L14.5033 14.8401C15.0764 14.1727 15.9263 13.75 16.875 13.75C18.6009 13.75 20 15.1491 20 16.875C20 18.6009 18.6009 20 16.875 20C15.1491 20 13.75 18.6009 13.75 16.875C13.75 16.5475 13.8004 16.2319 13.8938 15.9353L5.49674 12.0349C4.92362 12.7023 4.07368 13.125 3.125 13.125C1.39911 13.125 0 11.7259 0 10C0 8.27411 1.39911 6.875 3.125 6.875C4.07368 6.875 4.92362 7.29773 5.49674 7.96509L13.8938 4.06475C13.8004 3.76814 13.75 3.45245 13.75 3.125ZM3.125 8.125C2.08947 8.125 1.25 8.96447 1.25 10C1.25 11.0355 2.08947 11.875 3.125 11.875C4.16053 11.875 5 11.0355 5 10C5 8.96447 4.16053 8.125 3.125 8.125ZM16.875 15C15.8395 15 15 15.8395 15 16.875C15 17.9105 15.8395 18.75 16.875 18.75C17.9105 18.75 18.75 17.9105 18.75 16.875C18.75 15.8395 17.9105 15 16.875 15Z"
                                      fill="#9CA4B6"/>
                            </svg>
                            <svg class="ml-1" width="4" height="18" viewBox="0 0 4 18" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.875 15.25C3.875 16.2855 3.03553 17.125 2 17.125C0.964466 17.125 0.125 16.2855 0.125 15.25C0.125 14.2145 0.964466 13.375 2 13.375C3.03553 13.375 3.875 14.2145 3.875 15.25ZM3.875 9C3.875 10.0355 3.03553 10.875 2 10.875C0.964466 10.875 0.125 10.0355 0.125 9C0.125 7.96447 0.964466 7.125 2 7.125C3.03553 7.125 3.875 7.96447 3.875 9ZM3.875 2.75C3.875 3.78553 3.03553 4.625 2 4.625C0.964466 4.625 0.125 3.78553 0.125 2.75C0.125 1.71447 0.964466 0.875 2 0.875C3.03553 0.875 3.875 1.71447 3.875 2.75Z"
                                      fill="#9CA4B6"/>
                            </svg>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
<?php
echo $OUTPUT->footer();

