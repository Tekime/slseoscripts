<?php
/**
 * captcha.php - Displays a CAPTCHA image to the browser and sets the CAPTCHA session code
 *
 * Copyright (c) 2008 Intavant (http://www.intavant.com/), All Rights Reserved
 * 
 * IMPORTANT - This is not free software. You must adhere to the terms of the End-User License Agreement
 *             under penalty of law. Read the complete EULA in "license.txt" included with your application.
 * 
 *             This file can be used, modified and distributed under the terms of the Source License
 *             Agreement. You may edit this file on a licensed Web site and/or for private development.
 *             You must adhere to the Source License Agreement. The latest copy can be found online at:
 * 
 *             http://www.intavant.com/
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR 
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND 
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright   Copyright (c) 2008 phpLinkBid, All Rights Reserved
 * @license     http://www.intavant.com/
 * @author      Gabriel Harper, Intavant <gharper@intavant.com>
 * @version     1.1
 *
 * 1.1 - Added SESSION unset
 *
 */

require_once('config.php');
require_once(PATH_INCLUDE . 'header.php');

unset($_SESSION['captcha']);

captchaImageDisplay(120, 40, 5, PATH_INCLUDE . '/monofont.ttf');

require_once(PATH_INCLUDE . 'footer.php');

?>