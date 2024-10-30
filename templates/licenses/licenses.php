<?php
/**
 * Content for license page
 *
 * @package wpil\View
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<section class="laci-license-page">
    <header>
        <div class="laci-license__top-bar">
            <div class="laci-license__top-bar__icon">
            </div>
            <div class="laci-license__top-bar__title">
                <h2><?php esc_html_e( 'Activate Licenses', 'laci-link-cluster' ); ?></h2>
            </div>
            <div class="laci-license__important-notice">
                <span><svg viewBox="64 64 896 896" focusable="false" data-icon="info-circle" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm0 820c-205.4 0-372-166.6-372-372s166.6-372 372-372 372 166.6 372 372-166.6 372-372 372z" fill="#1890ff"></path><path d="M512 140c-205.4 0-372 166.6-372 372s166.6 372 372 372 372-166.6 372-372-166.6-372-372-372zm32 588c0 4.4-3.6 8-8 8h-48c-4.4 0-8-3.6-8-8V456c0-4.4 3.6-8 8-8h48c4.4 0 8 3.6 8 8v272zm-32-344a48.01 48.01 0 010-96 48.01 48.01 0 010 96z" fill="#e6f7ff"></path><path d="M464 336a48 48 0 1096 0 48 48 0 10-96 0zm72 112h-48c-4.4 0-8 3.6-8 8v272c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8V456c0-4.4-3.6-8-8-8z" fill="#1890ff"></path></svg></span>
                <span>
                    <strong><?php esc_html_e( 'Important notice:', 'laci-link-cluster' ); ?></strong>
                    <span><?php esc_html_e( 'Please activate your license to start using the Pro version', 'laci-link-cluster' ); ?></span>
                </span>
            </div>
        </div>
    </header>
    <main>
        <div class="laci-license-body">
            <div class="laci-license-layout">
                <div class="laci-license-layout-main">
                    <div class="laci-license-settings">
                        <div class="laci-license-card laci-license-no-license">
                            <div class="laci-license-card-body">
                                <?php esc_html_e( 'Welcome to Link and Cluster! Please visit our website to get more', 'laci-link-cluster' ); ?> <a href="https://linkandcluster.com/" target="_blank"><?php esc_html_e( 'Link and Cluster plugins', 'laci-link-cluster' ); ?></a> 👋
                            </div>
                        </div>
                        <?php do_action( 'laci_licenses_page' ); ?>
                    </div>
                </div>
                <div class="laci-license-sidebar">
                    <div class="laci-license-sidebar-sticky">
                        <div class="laci-license-card laci-license__getting-started">
                            <div class="laci-license-card-header">
                                <h3><?php esc_html_e( 'Getting Started (How to find license keys)', 'laci-link-cluster' ); ?></h3>
                            </div>
                            <div class="laci-license-card-body">
                                <p><?php esc_html_e( 'Please follow the steps below:', 'laci-link-cluster' ); ?></p>
                                <ul class="laci-license__task-list">
                                    <li>
                                        <span>
                                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#FFF8DB"/><path d="M12.3413 17H14.2583V7.84033H12.3477L9.97998 9.48438V11.2109L12.2271 9.64941H12.3413V17Z" fill="#F1C40F"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Log in to', 'laci-link-cluster' ); ?> <a href="https://wpil.com/dashboard" target="_blank"><?php esc_html_e( 'Link and Cluster Dashboard', 'laci-link-cluster' ); ?></a></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#FFF8DB"/><path d="M9.24365 17H15.8516V15.4575H11.8145V15.3115L13.5093 13.731C15.1787 12.1885 15.7183 11.3252 15.7183 10.2842V10.2651C15.7183 8.68457 14.3979 7.60547 12.5127 7.60547C10.5068 7.60547 9.13574 8.81152 9.13574 10.5698L9.14209 10.5952H10.9131V10.5635C10.9131 9.69385 11.5288 9.09717 12.4238 9.09717C13.2998 9.09717 13.833 9.64307 13.833 10.4238V10.4429C13.833 11.084 13.4839 11.5474 12.1953 12.7979L9.24365 15.7114V17Z" fill="#F1C40F"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Go to', 'laci-link-cluster' ); ?> <strong><?php esc_html_e( 'License Keys tab', 'laci-link-cluster' ); ?></strong></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#FFF8DB"/><path d="M12.5 17.2031C14.6392 17.2031 16.0674 16.0796 16.0674 14.4165V14.4038C16.0674 13.1597 15.1787 12.3726 13.814 12.2456V12.2075C14.8867 11.9854 15.7246 11.2427 15.7246 10.1001V10.0874C15.7246 8.62744 14.4551 7.63721 12.4873 7.63721C10.564 7.63721 9.27539 8.70361 9.14209 10.3413L9.13574 10.4175H10.9004L10.9067 10.3604C10.9829 9.59863 11.5859 9.10986 12.4873 9.10986C13.3887 9.10986 13.9155 9.57959 13.9155 10.3413V10.354C13.9155 11.0967 13.2935 11.6045 12.3286 11.6045H11.3066V12.9692H12.354C13.4648 12.9692 14.1187 13.4517 14.1187 14.3276V14.3403C14.1187 15.1147 13.4775 15.6606 12.5 15.6606C11.5098 15.6606 10.856 15.1528 10.7734 14.4419L10.7671 14.3721H8.93262L8.93896 14.4546C9.06592 16.0923 10.4307 17.2031 12.5 17.2031Z" fill="#F1C40F"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Copy your license key and paste it in this License tab', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="13" r="12" fill="#FFF8DB"/><path d="M13.2046 17H15.0327V15.3179H16.2261V13.7817H15.0327V7.84033H12.3413C11.1226 9.71924 9.87842 11.7505 8.7168 13.8135V15.3179H13.2046V17ZM10.4434 13.8325V13.731C11.2686 12.252 12.2524 10.6587 13.1411 9.29395H13.2427V13.8325H10.4434Z" fill="#F1C40F"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Done!', 'laci-link-cluster' ); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="laci-license-card laci-license__activate-steps">
                            <div class="laci-license-card-header">
                                <h3><?php esc_html_e( 'What you’ll get when activating license:', 'laci-link-cluster' ); ?></h3>
                            </div>
                            <div class="laci-license-card-body">
                                <ul class="laci-license__task-list">
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Start using the Pro version', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Auto-update to the latest version', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Get bug fixes and security updates fastest', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Custom CSS & theme tweaks upon requests', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Premium technical support', 'laci-link-cluster' ); ?></span>
                                    </li>
                                    <li>
                                        <span>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 1.25C5.16797 1.25 1.25 5.16797 1.25 10C1.25 14.832 5.16797 18.75 10 18.75C14.832 18.75 18.75 14.832 18.75 10C18.75 5.16797 14.832 1.25 10 1.25ZM13.7793 7.14258L9.66602 12.8457C9.60853 12.9259 9.53274 12.9913 9.44493 13.0364C9.35713 13.0815 9.25984 13.1051 9.16113 13.1051C9.06242 13.1051 8.96513 13.0815 8.87733 13.0364C8.78953 12.9913 8.71374 12.9259 8.65625 12.8457L6.2207 9.4707C6.14648 9.36719 6.2207 9.22266 6.34766 9.22266H7.26367C7.46289 9.22266 7.65234 9.31836 7.76953 9.48242L9.16016 11.4121L12.2305 7.1543C12.3477 6.99219 12.5352 6.89453 12.7363 6.89453H13.6523C13.7793 6.89453 13.8535 7.03906 13.7793 7.14258Z" fill="#52C41A"/></svg>
                                        </span>
                                        <span><?php esc_html_e( 'Live Chat 1-1 on Facebook for any questions', 'laci-link-cluster' ); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</section>

