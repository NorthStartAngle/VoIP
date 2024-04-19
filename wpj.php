<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WebRTC SIP Gateway</title>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/8.2.3/adapter.min.js" ></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" ></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.2/umd/popper.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js" ></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.6.0/js/md5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script type="text/javascript" src="janus/prod/settings.js" ></script>
<script type="text/javascript" src="janus/prod/janus.js" ></script>
<script type="text/javascript" src="janus/prod/sip.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.2/cerulean/bootstrap.min.css" type="text/css"/>
<link rel="stylesheet" href="assets/fonts/Poppins/Poppins.min.css">
<link id="mainCSS" rel="stylesheet" href="assets/css/wp/wp.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css"/>

<style>
 audio {
  height: 20px!important;
  width: 160px;
  margin-top: 5px;
 }
 article {
  display: inline-flex;
 }
</style>

 <script>
  const savedTheme = localStorage.getItem('theme');
  const devTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  const theme = savedTheme ? savedTheme : devTheme;

  if (theme == 'dark') document.write('<link id="dark_css" href="/assets/css/wp/dark.css" rel="stylesheet" type="text/css" />');

  window.addEventListener('storage', (e) => {
   if (e.key == 'theme') {
    const theme = e.newValue;

    if (theme == 'light') $('#dark_css').remove();
    else $('<link id="dark_css" rel="stylesheet" href="/assets/css/wp/dark.css" type="text/css" />').insertAfter('#mainCSS');
   }
  });
 </script>

</head>
<body>

<div id="webphone">
 <? if ($canDirectCall) { ?>
 <div id="txtPhoneNumber_wrapper">
  <? } else { ?>
  <div id="txtPhoneNumber_wrapper" style="display: none;">
   <? } ?>
   <div class="icon">
    <!-- Phone Icon -->
    <img src="assets/media/icons/svg/WebPhone/phone.svg" />
   </div>
   <input type="text" id="txtPhoneNumber" size="12" value="" placeholder="<?=$TEXT_Numero?>"
          autocomplete="off" />
  </div>

  <!-- Chiama -->
  <button id="btnCall" class="button-wrapper">
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/chiama.svg" />
   </div>
   <span><?=$TEXT_Chiama?></span>
  </button>

  <? if ($_SESSION['callType'] != 'conf') { ?>
  <!-- Riaggancia -->
  <button id="btnHangUp" onclick='sipHangUp();' class="button-wrapper hidden" disabled>
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/riaggancia.svg" />
   </div>
   <span><?=$TEXT_Riaggancia?></span>
  </button>
  <? } ?>

  <!-- Attiva muto -->
  <button id="btnMute" onclick='sipToggleMute();' class="button-wrapper off hidden" disabled>
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/attiva_muto.svg" />
   </div>

   <span><?=$TEXT_Attiva_muto?></span>
  </button>


  <!-- Cuffie -->
  <div id="btnCuffie" class="button-wrapper hidden">
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/cuffie.svg" />
   </div>

   <span><?=$TEXT_Cuffie?></span>

   <div class="controls">
    <img src="assets/media/icons/svg/WebPhone/decrease.svg" class="control" onclick='astSetVolume("less");' />
    <img src="assets/media/icons/svg/WebPhone/increase.svg" class="control" onclick='astSetVolume("more");' />
   </div>
  </div>

  <!-- Mic -->
  <div id="btnMic" class="button-wrapper hidden">
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/mic.svg" />
   </div>

   <span><?=$TEXT_Mic?></span>

   <div class="controls">
    <img src="assets/media/icons/svg/WebPhone/decrease.svg" class="control" onclick='astSetVolumeMic("less");' />
    <img src="assets/media/icons/svg/WebPhone/increase.svg" class="control" onclick='astSetVolumeMic("more");' />
   </div>
  </div>

  <!-- ET -->
  <button id="btnET" onclick='callEchoTest("call-audio");' class="button-wrapper off">
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/et.svg" />
   </div>
   <span>Echo Test</span>
  </button>
  <!-- <input type="button" value=" ET " onclick='callEchoTest("call-audio");' /> -->

  <!-- Tones -->
  <div id="showHideTonesBtnId" onclick='showHideTones();' class="button-wrapper off hidden" style="padding-right: 8px;">
   <div class="icon">
    <img src="assets/media/icons/svg/WebPhone/tones.svg" />
   </div>
   <span><?=$TEXT_Mostra_toni?></span>
  </div>

  <div>
   <div style="display: none;" id="tonesContId">
    <button onclick='event.stopPropagation(); sipSendDTMF("1");'>1</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("2");'>2</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("3");'>3</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("4");'>4</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("5");'>5</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("6");'>6</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("7");'>7</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("8");'>8</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("9");'>9</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("0");'>0</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("*");'>*</button>
    <button onclick='event.stopPropagation(); sipSendDTMF("#");'>#</button>
    &nbsp;&nbsp;&nbsp;
    <input type='text' name='tonesBox' id='tonesBox' oninput="this.value = this.value.replace(/[^0-9\#\*]/g, '').replace(/(\..*)\./g, '$1');"
     placeholder="<?=$TEXT_Digita_i_toni?>" autocomplete='off' size='10' />
    <button onclick='sendTones();'>&#8593;</button>
   </div>
  </div>
  <!-- <input type="button" id="showHideTonesBtnId" value="<?=$TEXT_Mostra_toni?>" onclick='showHideTones();' /> -->

  <label id="txtCallStatus"></label>
  <label id="txtStats" style="display: flex; align-items: center; white-space: nowrap; font-size: .8em;"></label>

    <?
    if ($_SERVER['HTTP_HOST'] == 'dev1.sidial.cloud' || $_SERVER['HTTP_HOST'] == 'testing.sidial.cloud' || $_SERVER['HTTP_HOST'] == 'testing02.sidial.cloud') {
    ?>
    <section class="main-controls">
    <canvas class="visualizer" style="height: 70vh; width: 100px; float: left; display: none;"></canvas>
    <div id="buttons" style="float: left; margin-left: 15px;">
     <button class="record2">Record</button>
     <button class="stopRec2" disabled="">Stop</button>
    </div>
    </section>

    <section class="sound-clips" style="height: 24px;"></section>
    <?
    }
    ?>
 </div>

 <audio id="audio_remote" autoplay="autoplay"></audio>
 <audio id="ringtone" loop src="sounds/ringtone.wav"></audio>
 <audio id="ringbacktone" loop src="sounds/ringbacktone.wav"></audio>
 <audio id="dtmfTone" src="sounds/dtmf.wav"></audio>

	<input type="hidden" id="server" value="sip:dev2.sidial.cloud" />
	<input type="hidden" id="username" value="sip:100@dev2.sidial.cloud" />
	<input type="hidden" id="authuser" value="100" />
	<input type="hidden" id="password" value="cu7ZDfFh.k" />
	<input type="hidden" id="displayname" value="100" />
	<input type="hidden" id="registerset" value="secret" />
	<input type="hidden" id="peer" value="sip:810@dev2.sidial.cloud" />

</body>

</html>
