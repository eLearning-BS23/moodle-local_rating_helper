<p align="center">
  <a href="#">
    <img src="https://user-images.githubusercontent.com/38932580/173321324-467aada5-c93b-430b-81a0-b247c451658a.svg" alt="Logo" width="80" height="80">
  </a>
</p>

# Moodle Course Rating Helper

The plugin is used to submit ratings of individual course. It also aggregates ratings of all user including the number
of
people who have already submitted ratings.

## Features

- Display course wise Rating
- User can make a comment
- Average rating display
- smooth loading
- Easy to install
- Access from anywhere with additional url

## Prerequisites

- Moodle >= 3.8.9
- PHP >= 7.3

## Installing via Plugin Directory ##

You can install this plugin from [Moodle plugins directory](https://moodle.org/plugins) or can download
from [Github](https://github.com/eLearning-BS23/moodle-local_rating_helper/releases/latest).

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/rating_helper

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## How to use

- Just install plugin and visit the **Rating Helper** Page

![image](https://user-images.githubusercontent.com/38932580/173322018-9c348cf2-d28a-4639-9e7c-dbc69e409783.png)

You will get a course List as below

![image](https://user-images.githubusercontent.com/38932580/173322258-de13553a-6370-4a3d-900a-81128b040bf4.png)

**Rate Now** Submit new rating.

**Copy URL** Copy the Course Rating Submit Page URL to use anywhere.

**Ratings** Get a list of specific course ratings.

![image](https://user-images.githubusercontent.com/38932580/173326390-dfe70b00-9353-4fdb-aa27-c3efb40e3e53.png)

## Rating Form

<p align="left">
<img src="https://camo.githubusercontent.com/a7fbb1d587c2d6cc1b598b115b94e3eea9e0087eea5f06ad79e75e9dd59f3fae/68747470733a2f2f692e696d6775722e636f6d2f69334d495251392e706e67">
</p>

## Rating Display

<p align="left">
<img src="https://camo.githubusercontent.com/a5197b5a91730a970d6a901da9ed526b3a22d94d08f16a8f74f8af605651ac1d/68747470733a2f2f692e696d6775722e636f6d2f354441376376452e706e67">
</p>

Each user can rate a course only once. If they visit the ratings form again, a message is displayed all courses and hide
form displays the rating they gave.

- That's it. and you are done!
- Enjoy the plugin!

<!-- CONTRIBUTING -->

## Contributing

Contributions are what makes the open source community such an amazing place to learn, inspire, and create. Any
contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License ##

2022 Brain Station 23

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.






