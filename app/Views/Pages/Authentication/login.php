<?php
    require_once './config/config.php'; 

    $pageTitle = APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php 
        require_once './app/Views/Includes/head-meta-tags.php'; 
        require_once './app/Views/Includes/head-stylesheet.php';
    ?>
</head>

<?php include_once './app/Views/Includes/theme-script.php'; ?>

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on">
    <?php include_once './app/Views/Includes/preloader.php'; ?>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-5 p-lg-12">
                <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-15">
                    <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-100">
                        <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                            <form class="form w-100" id="signin-form" method="POST">
                                <img src="./assets/images/logos/logo-dark.svg" class="mb-5" alt="Logo-Dark" />
                                <h2 class="mb-2 mt-4 fs-1 fw-bolder">Welcome to <?php echo APP_NAME ?></h2>
                                <p class="mb-10 fs-5">Fueling digital growth, empowering your success</p>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" autocomplete="off">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button class="btn btn-light bg-transparent password-addon" type="button">
                                            <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                    <a href="/forgot" class="link-primary">Forgot Password?</a>
                                </div>

                                <div class="d-grid">
                                    <button id="signin" type="submit" class="btn btn-primary">Sign In</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-lg-row-fluid">
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="./assets/images/auth/agency.png" alt=""/>    
                    <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="./assets/images/auth/agency-dark.png" alt=""/>
                    
                    <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7"> 
                        Fast, Efficient and Productive
                    </h1>

                    <div class="text-gray-600 fs-base text-center fw-semibold">
                        In this kind of post, <a href="#" class="opacity-75-hover text-primary me-1">the blogger</a> 

                        introduces a person they’ve interviewed <br/> and provides some background information about 
                        
                        <a href="#" class="opacity-75-hover text-primary me-1">the interviewee</a> 
                        and their <br/> work following this is a transcript of the interview.  
                    </div>
                </div>
            </div>
        </div>
    </div>   

    <?php 
        include_once './app/Views/Includes/error-modal.php';
        require_once './app/Views/Includes/required-js.php';        
    ?>

    <script type="module" src="./assets/js/pages/login.js?v=<?php echo rand(); ?>"></script>
</body>
</html>