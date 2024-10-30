(function( $ ) {
    'use strict';

    $(document).ready(function () {
        // Declare a variable to store the interval ID        
        let progressInterval;
        const nonce = kleverlist_object.nonce;
        const totalCustomers = parseInt(kleverlist_object.totalCustomers);
        console.log(`Type is ${typeof totalCustomers} and total is ${totalCustomers}`);
        const formInput = "form#kleverlist_mailchimp_wc_migration_settings :input";

        const storageKey = 'mailchimpMigrationProgress';
        const responseProgressBar = '.progress-container';

        // Initialize pageLoadProgress from local storage
        let pageLoadProgress = parseInt(localStorage.getItem(storageKey));

        // Function to update the progress bar and text        
        let progress = 0;

        // Function to set progress in local storage
        function setProgressInLocalStorage(progress) {
            if (progress > 1 && progress < 100) {
                localStorage.setItem(storageKey, progress);
            } else {
                localStorage.removeItem(storageKey);
            }
        }

        // Function to update the progress bar dynamically
        function updateProgressBar(progress) {
            const progressBar = document.getElementById("migration-progress-bar");
		    if (progressBar) { // Check if the element exists
		        progressBar.style.width = progress + "%";
		        progressBar.textContent = progress + "%";
		    }
        }

        // Initialize progress bar on page load
        updateProgressBar(progress);

        $(document).on('submit', '#kleverlist_mailchimp_wc_migration_settings', function (e) {
            e.preventDefault();
            kleverlistMailchimpMigrationSettings();
        });

        // Function to update the progress bar and text
        function kleverlistMailchimpMigrationUpdateProgress() {
            let responseClass = '.kleverlist-migration-response';

            $(formInput).each(function () {
                $(this).attr("disabled", "disabled");
            });

            if (progress < 100) {
                // Update progress in local storage
                $(responseProgressBar).show();
                setProgressInLocalStorage(progress);
                kleverlistMailchimpCheckProgressMigration();
            }

            if (progress == 100) {
                clearInterval(progressInterval); // Clear the interval using the interval ID
                kleverlistMailchimpMigrationDeleteOption();

                // Remove the progress from local storage
                setProgressInLocalStorage(0);

                // Set the migration completed flag
        		localStorage.setItem('mailchimpMigrationCompleted', 'true');

                setTimeout(function () {
                    $(responseClass).addClass('success');
                    $(responseClass).html('Progress Completed');
                    location.reload();
                }, 2500);
            }
        }

        function kleverlistMailchimpCheckProgressMigration() {
            $.ajax({
                url: ajaxurl,
                type: 'GET',
                data: {
                    action: 'kleverlist_mailchimp_check_progress_migration',
                    security: nonce,
                },
                success: function (response) {
                    if (response.status) {
                        progress = parseInt(response.progress); // Convert progress to an integer
                        pageLoadProgress = progress;

                        // Update the local storage value as well
                		setProgressInLocalStorage(progress);

                        // Update the progress bar dynamically
                        updateProgressBar(progress);
                    }
                },
                error: function (xhr, status, error) {
                    return false;
                }
            });
        }

        function kleverlistMailchimpMigrationDeleteOption() {
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'kleverlist_mailchimp_reset_migration_progress',
                    security: nonce,
                },
                success: function (response) {
                    if (response.status) {
                        console.log("deleted");
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                    return false;
                }
            });
        }

        function kleverlistMailchimpMigrationSettings() {
            let responseClass = '.kleverlist-migration-response';

            let kleverlist_mailchimp_audience_id = $('#kleverlist_mailchimp_audience_id').data('audienceid');
            
            if (kleverlist_mailchimp_audience_id == '') {
                $(responseClass).addClass('error');
                $(responseClass).html("Please select list");

                setTimeout(function () {
                    $(responseClass).html('');
                }, 2000);
                return false;
            }else if( totalCustomers ===0 ){
                $(responseClass).addClass('error');
                $(responseClass).html("There is no customer in your site");

                setTimeout(function () {
                    $(responseClass).html('');
                }, 2000);
                return false;
            } else {
                $(formInput).each(function () {
                    $(this).attr("disabled", "disabled");
                });

                $(responseProgressBar).show();

                progressInterval = setInterval(kleverlistMailchimpMigrationUpdateProgress, 3000);
                let preventContacts = ( $("#kleverlist_mailchimp_prevent_existing_contacts").prop('checked') == true ) ? 'yes' : 'no';
              
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'kleverlist_mailchimp_migration_settings',
                        kleverlist_mailchimp_audience_id: kleverlist_mailchimp_audience_id,
                        preventContacts: preventContacts,
                        security: nonce,
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log("Response", response);
                        if (response != '') {
                            $(responseClass).removeClass('success');
                            $(responseClass).removeClass('error');
                            $(responseClass).html('');

                            if (response.status === true && response.progressUpdates) {
                                console.log(response.progressUpdates);
                            } else if (response.status === false && response.message) {
                                $(responseProgressBar).hide();
                                $(responseClass).addClass('error');
                                $(responseClass).html(response.message);
                                setTimeout( function () {
                                    location.reload();          
                                }, 1500 );
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle AJAX error
                    },
                });
            }
        }

        // Function to check if migration is completed
	    function isMigrationCompleted() {
	        return localStorage.getItem('mailchimpMigrationCompleted') === 'true';
	    }
        
		if (!isNaN(pageLoadProgress) && pageLoadProgress < 100) {
		    // Continuously update the progress as long as it's not 100%
		    progressInterval = setInterval(function () {
		        kleverlistMailchimpMigrationUpdateProgress();
		    }, 1000);
		}
    });

})(jQuery);
