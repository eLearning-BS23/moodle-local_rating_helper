<?php
global $DB, $USER, $OUTPUT, $CFG, $PAGE;
require_once("../../config.php");
require_once($CFG->dirroot . '/local/rating_helper/lib.php');

require_login();
//$course_id = required_param('course_id', PARAM_INT);
//$course = $DB->get_record("course", array("id" => $course_id), "*", MUST_EXIST);
//$sql = 'select * from {local_rating_helper} where cmid = ' . $course_id . ' AND userid=' . $USER->id;
//$israting = $DB->get_record_sql($sql);
//$imageurl = get_course_image_url($course_id);
//$courseurl = $CFG->wwwroot . '/course/view.php?id=' . $course_id;
//$PAGE->set_url('/rating_helper/index.php');
//$PAGE->set_context(context_system::instance());
//$PAGE->set_pagetype('my-index');
//$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
//$PAGE->set_heading(get_string('pluginname', 'local_rating_helper'));
//$PAGE->navbar->add($course->fullname, $courseurl);
//$PAGE->navbar->add(get_string('ratings', 'local_rating_helper'));
//echo $OUTPUT->header();


$course_id = required_param('course_id', PARAM_INT);
$course = $DB->get_record("course", array("id" => $course_id), "*", MUST_EXIST);
$sql = 'select * from {local_rating_helper} where cmid = ' . $course_id . ' AND userid=' . $USER->id;
$israting = $DB->get_record_sql($sql);
$courseurl = $CFG->wwwroot . '/course/view.php?id=' . $course_id;
$context = context_system::instance();
$PAGE->set_url('/rating_helper/index.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
$PAGE->set_heading(get_string('pluginname', 'local_rating_helper'));
$PAGE->navbar->add($course->fullname, $courseurl);
$PAGE->navbar->add(get_string('ratings', 'local_rating_helper'));
echo $OUTPUT->header();
$imageurl = get_course_image_url($course_id);
$paramsforratinguse = [
    'course_id' => $course_id,
    'user_id' => $USER->id,
];
$PAGE->requires->js_call_amd('local_rating_helper/submitrating', 'init', $paramsforratinguse);
?>
    <style xmlns="http://www.w3.org/1999/html">

        .person img{
            height: 50px;
            width: 50px;
            vertical-align: middle;
        }
        .custom-rounded-top {
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }

        .custom-rounded {
            border-radius: 0.75rem !important;
        }

        .section-part {
            background-color: #FFFFFF;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        .section-part-nav {
            color: #868686;
            font-size: small;
            font-weight: 400;
        }

        .section-part-nav-learning {
            font-size: small;
            color: #303030;
            font-weight: 500;
            text-decoration: none;
        }

        .title-name {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-weight: 500;
            font-size: 36px;
            color: #9CA4B6;
        }

        .left-upper-part {
            display: flex;
        }

        .five {
            color: #666666;
            font-weight: 700;
        }

        /*.star {*/
        /*    margin-left: 10px;*/
        /*}*/

        .star-icon {
            color: #9CA4B6;
            padding-top: 10px;

        }

        .based-on {
            color: #666666;
            /* border-bottom: 1px solid #666666; */
        }

        .star-of-star {
            border-top: 1px solid #666666;
        }

        .five-star {
            color: #9CA4B6;
        }

        .four-star {
            color: #9CA4B6;
            padding-left: 28px;
        }

        .three-star {
            color: #9CA4B6;
            padding-left: 56px;
        }

        .two-star {
            color: #9CA4B6;
            padding-left: 84px;
        }

        .one-star {
            color: #9CA4B6;
            padding-left: 112px;
        }

        .middle-part {
            padding: 0px 20px;
            margin-left: 3px;
            border-left: 1px solid #666666;
        }

        .feedback {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-weight: 500;
            font-size: 30px;
            color: #9CA4B6;
        }

        .experience {
            color: #666666;
        }

        .star-icon-rate {
            color: #9CA4B6;
        }

        /* .review-section{
            display: flex;
            flex-direction: column;
        } */
        .review {
            font-weight: 400;
            font-size: small;
            color: #666666;
        }

        .comment {
            margin-left: 15px;
            margin-top: 10px;
            font-weight: 400;
            font-size: small;
            color: #666666;
        }

        .send {
            width: 120px;
            height: 30px;
            border-radius: 15px;
            border: 1px solid #868686;
            text-align: center;
            align-content: center;
            padding-bottom: 7px;
            margin-top: 10px;
            margin-left: 395px;
            margin-bottom: 15px;
        }

        .send-rate {
            font-size: small;
            margin-bottom: 3px;
            font-weight: 500;
            font-style: normal;
        }

        .middle-last-part {
            border-top: 1px solid #868686;
            justify-content: center;
        }

        .pre-rate {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            font-weight: 500;
            font-size: 30px;
            color: #9CA4B6;
            margin-top: 20px;
        }

        .person-rating {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .person-name {
            margin-right: 8px;
            margin-left: 3px;
            margin-top: 16px;
            color: #666666;
            font-size: 20px;
            font-weight: 400;
            font-style: normal;
        }

        .date {
            color: #B4BBC6;
            font-size: 14px;
            font-weight: 400;
            font-style: normal;
        }

        .comment-of-person {
            color: #666666;
            font-weight: 400;
            font-size: 16px;
            font-style: normal;
        }

        .last-image {
            width: 100%;
            height: 160px;
            padding: 0px;
            margin-bottom: 2px;
        }

        .eye {
            color: #9CA4B6;
            font-size: 18px;
            font-style: normal;
            font-weight: 700;
            padding: 2px;
        }

        .suffer {
            color: #B5BBC8;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
        }

        .suffer-from {
            margin-bottom: 10px;
            padding: 2px;
        }

        .progress-section {
            display: flex;
        }

        .progress {
            width: 180px;
        }

        .progress-bar {
            background-color: #666666;

        }

        .concluded {
            font-size: x-small;
            font-weight: 400;
            color: #B5BBC8;
            font-style: normal;
            margin-left: 4px;
        }

        .icon-last {
            justify-content: right;
            text-align: right;
            padding: 3px;
            margin: 5px;
            padding-top: 7px;
        }

        .icon {
            color: #9CA4B6;
            width: 20px;
            height: 20px;
            justify-content: space-between;
        }

        /*ration css*/

        div.stars {
            /*width: 270px;*/
            display: inline-block
        }

        .mt-200 {
            margin-top: 200px
        }

        input.star {
            display: none
        }

        label.star {
            float: right;
            padding-right: 5px;
            font-size: 20px;
            color: #4A148C;
            /*transition: all .2s*/
        }

        input.star:checked ~ label.star:before {
            content: '\f005';
            color: #FD4;
            transition: all .25s
        }

        input.star-5:checked ~ label.star:before {
            /*color: #FE7;*/
            /*text-shadow: 0 0 20px #952*/
        }

        input.star-1:checked ~ label.star:before {
            color: #F62
        }

        /*label.star:hover {*/
        /*    transform: rotate(-15deg) scale(1.3)*/
        /*}*/

        label.star:before {
            content: '\f006';
            font-family: FontAwesome
        }
    </style>
    <script src="https://kit.fontawesome.com/193f3c6547.js" crossorigin="anonymous"></script>

    <section class="container-fluid section-part">
        <div class="ratings-title mb-4">
            <h3 class="title-name"><?= get_string('ratings', 'local_rating_helper') ?></h3>
        </div>
        <div class="row m-0">

            <div class="col-md-3 col-12 p-0 ">
                <div class="left-part">

                    <div class="left-upper-part">

                        <h1 id="avg_rating" class="five">0.0</h1>

                        <div class="star">
                            <h3 id="avg_ret_star" class="star-head">
                            </h3>
                        </div>


                    </div>
                    <p class="based-on"><?= get_string('basedon', 'local_rating_helper') ?> <b
                                id="total-rating"></b> <?= get_string('ratings', 'local_rating_helper') ?></p>
                </div>

                <div class="star-of-star">
                    <div class="star">
                        <h5 class="star-head">
                            <i class="far fa-star star-icon"></i>
                            <i class="far fa-star star-icon"></i>
                            <i class="far fa-star star-icon"></i>
                            <i class="far fa-star star-icon"></i>
                            <i class="far fa-star star-icon"></i>
                            <span class="five-star">| 5 star(<span id="five-star"></span>)</span>
                        </h5>

                    </div>
                    <div>
                        <div class="star">
                            <h5 class="star-head">
                                <i class="far fa-star star-icon"></i>
                                <i class="far fa-star star-icon"></i>
                                <i class="far fa-star star-icon"></i>
                                <i class="far fa-star star-icon"></i>

                                <span class="four-star">| 4 star(<span id="four-star"></span>)</span>
                            </h5>

                        </div>
                        <div>
                            <div class="star">
                                <h5 class="star-head">
                                    <i class="far fa-star star-icon"></i>
                                    <i class="far fa-star star-icon"></i>
                                    <i class="far fa-star star-icon"></i>

                                    <span class="three-star">| 3 star(<span id="three-star"></span>)</span>
                                </h5>

                            </div>
                            <div>
                                <div class="star">
                                    <h5 class="star-head">
                                        <i class="far fa-star star-icon"></i>
                                        <i class="far fa-star star-icon"></i>

                                        <span class="two-star">| 2 star(<span id="two-star"></span>)</span>
                                    </h5>

                                </div>
                                <div>
                                    <div class="star">
                                        <h5 class="star-head">
                                            <i class="far fa-star star-icon"></i>
                                            <span class="one-star">| 1 star(<span id="one-star"></span>)</span>
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
                                <h4 class="feedback"><?= get_string('whatyourfeedback', 'local_rating_helper') ?></h4>
                                <p class="experience"><?= get_string('rateyourexperience', 'local_rating_helper') ?></p>

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
                                        <span class="review"><?= get_string('leaveareview', 'local_rating_helper') ?></span>
                                        <div class="comment-section">
                                <textarea name="comment" id="comment" class="custom-rounded p-3 w-100" rows="4"
                                          maxlength="200"
                                          placeholder="<?= get_string('commentplaceholder', 'local_rating_helper') ?>"></textarea>
                                        </div>
                                        <div class="text-right mt-4 mb-4">
                                            <button type="button" id="submit_rating_form"
                                                    class="custom-rounded btn btn-light"><?= get_string('sendrate', 'local_rating_helper') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <hr>
                            <?php
                        }
                        ?>
                        <h4 class="pre-rate"><?= get_string('previousratings', 'local_rating_helper') ?></h4>
                        <div id="user-ratings">

                        </div>
                    </div>


                </div>
            </div>


            <div class="col-md-3 col-12">
                <div class="card custom-rounded w-100" style="width: 18rem;">

                    <img class="custom-rounded-top"
                         src="<?= $imageurl ?>"
                         alt="Card image cap">
                    <div class="card-body">

                        <h5 class="eye"><a href="<?= $courseurl ?>"><?php echo $course->fullname; ?> </a></h5>

                        <div class="suffer-from">
                            <span class="suffer"><?php echo html_entity_decode($course->summary); ?></span>
                        </div>
                        <div class="progress-section d-flex mt-4" style="line-height: 6px;">
                            <div class="progress" style="height:5px; width:65%;">
                                <div class="progress-bar" role="progressbar" style="width: 80%"
                                     aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="concluded">100% concluded</div>

                        </div>
                        <div class="icon-last float-right mt-2">
                            <i class="icon fas fa-share-alt"></i>
                            <i class="icon fas fa-ellipsis-v"></i>
                        </div>

                    </div>
                </div>

            </div>
        </div>
<?php
echo $OUTPUT->footer();

