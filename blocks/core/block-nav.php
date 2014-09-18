<?php
require_once("library/core/class-menu.php");
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
skin_include("block-nav-pre.php");
// Make menu
$topnav = new akMenu("top-nav","top-nav-div");
// Add any skin-specific bits
if (isset($top_nav_prefix)) $topnav->prefix = $top_nav_prefix;
if (isset($top_nav_postfix)) $topnav->postfix = $top_nav_postfix;
// Add meta-items
if ($auth_user->name=='default')
{
   $topnav->createItem("top-nav-meta","Login","/login.php");
}
else
{
  $topnav->createItem("top-nav-meta","Logout","/logout.php?auth-action=logout");
  $account = new akMenuItem("top-nav-meta","Account","/account.php");
  $topnav->adopt($account);
   /* ACCOUNT popup */
  $accountPopup = new akMenu("menu-popup","top-nav-account-popup");
  $accountPopup->createSimpleItem("Bookshelf","/bookshelf.php");
  $accountPopup->createSimpleItem("Profile","/account-profile.php");
  $accountPopup->createSimpleItem("Preferences", "/account-preferences.php");
  $account->adopt($accountPopup);
}
// Add regular items
$topnav->createSimpleItem("Home","/","Journey of Faith home");
$about = new akMenuItem("top-nav","About Us","/about.php","Who we are");
$topnav->adopt($about);
$topnav->createSimpleItem("Our Vision","/vision.php","How we see it");
$prog = new akMenuItem("top-nav", "Programs","/programs.php","How to participate");
$topnav->adopt($prog);
$topnav->createSimpleItem("Worship","/worship.php","When and how we worship together");
$mission = new akMenuItem("top-nav","Mission","/mission.php","Mission");
$topnav->adopt($mission);

// Add popup menus 
  /* ABOUT */
$aboutPopup = new akMenu("menu-popup","top-nav-about-popup");
$aboutPopup->createSimpleItem("Overview","/about.php");
$aboutPopup->createSimpleItem("Staff","/about-staff.php");
$aboutPopup->createSimpleItem("Committees","/about-committees.php");
$aboutPopup->createSimpleItem("Affiliations","/about-affiliations.php");
$aboutPopup->createSimpleItem("Support","/about-support-us.php");
$about->adopt($aboutPopup);

  /* PROGRAMS */
//$progPopup = new akMenu("menu-popup","top-nav-programs-popup");
//$progPopup->createSimpleItem("Adults","/programs-adults.php");
//$progPopup->createSimpleItem("Youth","/programs-youth.php");
//$progPopup->createSimpleItem("Children","/programs-children.php");
//$prog->adopt($progPopup);

  /* MISSION */
$missionPopup = new akMenu("menu-popup","top-nav-mission-popup");
$missionPopup->createSimpleItem("Overview","/mission.php");
$missionPopup->createSimpleItem("Youth","/mission-youth.php");
$missionPopup->createSimpleItem("Community","/mission-community.php");
$mission->adopt($missionPopup);

// Emit the menu
$topnav->emit();

?>
<?php
// If the skin has post-content setup, include it here.
skin_include("block-nav-post.php");
?>
