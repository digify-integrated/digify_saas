<?php
    require_once 'config/config.php';
    require_once './app/Views/Includes/session.php'; 
    require_once 'autoload.php';    

    use App\Models\Authentication;
    use App\Models\MenuItem;
    use App\Models\AppModule;
    use App\Core\Security;
    use App\Helpers\SystemHelper;

    $security = new Security();
    $authentication = new Authentication();
    $menuItem = new MenuItem();
    $appModule = new AppModule();
    $systemHelper = new SystemHelper();

    $loginCredentialsDetails = $authentication->getLoginCredentials($userID, null);
    $userFileAs = $loginCredentialsDetails['file_as'];
    $userAccountName = $loginCredentialsDetails['username'];
    $userEmail = $loginCredentialsDetails['email'];
    $multipleSession = $loginCredentialsDetails['multiple_session'];
    $profilePicture = $systemHelper->checkImage($loginCredentialsDetails['profile_picture'] ?? null, 'profile');
    $sessionToken = $security->decryptData($loginCredentialsDetails['session_token']);
    $isActive = $security->decryptData($loginCredentialsDetails['active']);
    $isLocked = $security->decryptData($loginCredentialsDetails['locked']);
    
    if ($isActive === 'No' || $isLocked === 'Yes' || ($security->decryptData($_SESSION['session_token']) != $sessionToken && $multipleSession === 'No')) {
        header('location: logout.php?logout');
        exit;
    }
?>