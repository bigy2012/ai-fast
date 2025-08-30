/* Ai Expert Ads Admin JavaScript */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        AiExpertAds.init();
        
        // Clear form if there's a success message
        if ($('.notice-success').length > 0) {
            // Clear the form if we're not in edit mode
            var form = $('.aea-account-form');
            if (form.find('input[name="action"]').val() === 'add_account') {
                form[0].reset();
                // Reset select to first option
                form.find('select').prop('selectedIndex', 0);
                // Check the status checkbox by default
                form.find('#status').prop('checked', true);
            }
        }
    });

    // Main plugin object
    window.AiExpertAds = {
        
        // Initialize the plugin
        init: function() {
            this.bindEvents();
            this.initFormValidation();
            this.initTooltips();
        },

        // Bind all events
        bindEvents: function() {
            // Account form submission
            $('#ai-expert-ads-account-form').on('submit', this.handleAccountSubmission);
            
            // Delete account confirmation
            $('.aea-delete-account').on('click', this.confirmAccountDeletion);
            
            // Toggle account status
            $('.aea-toggle-status').on('change', this.toggleAccountStatus);
            
            // Real-time account ID validation
            $('#account_id').on('blur', this.validateAccountId);
            
            // Settings form changes
            $('.aea-settings-form input, .aea-settings-form select').on('change', this.markSettingsChanged);
        },

        // Handle account form submission
        handleAccountSubmission: function(e) {
            var form = $(this);
            var submitBtn = form.find('input[type="submit"]');
            var originalText = submitBtn.val();

            // Validate form
            if (!AiExpertAds.validateAccountForm(form)) {
                e.preventDefault();
                return false;
            }

            // Show loading state
            submitBtn.val('Processing...').prop('disabled', true);
            form.find('.aea-loading').remove();
            submitBtn.after('<span class="aea-loading"></span>');

            // Allow form to submit normally
            // After successful submission, the page will reload and show success message
        },

        // Confirm account deletion
        confirmAccountDeletion: function(e) {
            var accountName = $(this).data('account-name') || 'this account';
            var message = 'Are you sure you want to delete "' + accountName + '"? This action cannot be undone.';
            
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        },

        // Toggle account status
        toggleAccountStatus: function() {
            var checkbox = $(this);
            var accountId = checkbox.data('account-id');
            var isActive = checkbox.is(':checked');
            
            // Send AJAX request to update status
            $.post(ajaxurl, {
                action: 'aea_toggle_account_status',
                account_id: accountId,
                status: isActive ? 1 : 0,
                nonce: $('#aea_nonce').val()
            })
            .done(function(response) {
                if (response.success) {
                    AiExpertAds.showNotice('Account status updated successfully!', 'success');
                } else {
                    AiExpertAds.showNotice('Failed to update account status.', 'error');
                    checkbox.prop('checked', !isActive); // Revert checkbox
                }
            })
            .fail(function() {
                AiExpertAds.showNotice('Network error occurred.', 'error');
                checkbox.prop('checked', !isActive); // Revert checkbox
            });
        },

        // Validate account ID
        validateAccountId: function() {
            var accountId = $(this).val().trim();
            var feedback = $('#account_id_feedback');
            
            // Remove previous feedback
            feedback.remove();
            $(this).removeClass('aea-field-error');

            if (accountId.length === 0) {
                return;
            }

            // Basic validation patterns
            var patterns = {
                'google-ads': /^\d{10}$/,
                'facebook-ads': /^\d{15,16}$/,
                'bing-ads': /^\d{8,9}$/
            };

            var accountType = $('#account_type').val();
            var pattern = patterns[accountType];

            if (pattern && !pattern.test(accountId)) {
                $(this).addClass('aea-field-error');
                $(this).after('<span id="account_id_feedback" class="aea-error-message">Invalid format for ' + accountType + ' account ID</span>');
            }

            // Check if account ID already exists
            $.post(ajaxurl, {
                action: 'aea_check_account_exists',
                account_id: accountId,
                nonce: $('#aea_nonce').val()
            })
            .done(function(response) {
                if (response.exists) {
                    $('#account_id').addClass('aea-field-error');
                    $('#account_id_feedback').remove();
                    $('#account_id').after('<span id="account_id_feedback" class="aea-error-message">This account ID already exists</span>');
                }
            });
        },

        // Validate entire account form
        validateAccountForm: function(form) {
            var isValid = true;
            var requiredFields = ['account_name', 'account_id'];

            // Clear previous errors
            form.find('.aea-field-error').removeClass('aea-field-error');
            form.find('.aea-error-message').remove();

            // Check required fields
            requiredFields.forEach(function(fieldName) {
                var field = form.find('[name="' + fieldName + '"]');
                var value = field.val().trim();

                if (value.length === 0) {
                    field.addClass('aea-field-error');
                    field.after('<span class="aea-error-message">This field is required</span>');
                    isValid = false;
                }
            });

            // Validate account ID format if present
            var accountIdField = form.find('#account_id');
            if (accountIdField.val().trim().length > 0 && accountIdField.hasClass('aea-field-error')) {
                isValid = false;
            }

            return isValid;
        },

        // Mark settings as changed
        markSettingsChanged: function() {
            var saveButton = $('.aea-settings-form input[type="submit"]');
            if (saveButton.val() !== 'Save Changes *') {
                saveButton.val('Save Changes *').addClass('button-primary');
            }
        },

        // Initialize tooltips
        initTooltips: function() {
            // Add tooltips to help icons
            $('.aea-help-tip').hover(
                function() {
                    var tooltip = $(this).data('tip');
                    $(this).after('<div class="aea-tooltip">' + tooltip + '</div>');
                },
                function() {
                    $('.aea-tooltip').remove();
                }
            );
        },

        // Initialize form validation
        initFormValidation: function() {
            // Real-time validation for account name
            $('#account_name').on('input', function() {
                var value = $(this).val().trim();
                var feedback = $('#account_name_feedback');
                
                feedback.remove();
                $(this).removeClass('aea-field-error');

                if (value.length > 50) {
                    $(this).addClass('aea-field-error');
                    $(this).after('<span id="account_name_feedback" class="aea-error-message">Account name must be 50 characters or less</span>');
                }
            });

            // Account type change handler
            $('#account_type').on('change', function() {
                var accountType = $(this).val();
                var accountIdField = $('#account_id');
                var placeholder = '';

                // Update placeholder based on account type
                switch(accountType) {
                    case 'google-ads':
                        placeholder = 'e.g., 1234567890 (10 digits)';
                        break;
                    case 'facebook-ads':
                        placeholder = 'e.g., 123456789012345 (15-16 digits)';
                        break;
                    case 'bing-ads':
                        placeholder = 'e.g., 12345678 (8-9 digits)';
                        break;
                    default:
                        placeholder = 'Enter your account ID';
                }

                accountIdField.attr('placeholder', placeholder);
                
                // Revalidate account ID if it has a value
                if (accountIdField.val().trim().length > 0) {
                    accountIdField.trigger('blur');
                }
            });
        },

        // Show admin notice
        showNotice: function(message, type) {
            var noticeClass = type === 'error' ? 'notice-error' : 'notice-success';
            var notice = '<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>';
            
            $('.wrap h1').after(notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.notice.is-dismissible').fadeOut();
            }, 5000);
        },

        // Format number with commas
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },

        // Copy to clipboard functionality
        copyToClipboard: function(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    AiExpertAds.showNotice('Copied to clipboard!', 'success');
                });
            } else {
                // Fallback for older browsers
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    AiExpertAds.showNotice('Copied to clipboard!', 'success');
                } catch (err) {
                    AiExpertAds.showNotice('Failed to copy to clipboard', 'error');
                }
                document.body.removeChild(textArea);
            }
        }
    };

    // Add copy functionality to account IDs
    $(document).on('click', '.aea-copy-account-id', function(e) {
        e.preventDefault();
        var accountId = $(this).data('account-id');
        AiExpertAds.copyToClipboard(accountId);
    });

    // Bulk actions handler
    $(document).on('change', '#bulk-action-selector-top', function() {
        var action = $(this).val();
        var selectedItems = $('input[name="account_ids[]"]:checked');
        
        if (action === 'delete' && selectedItems.length > 0) {
            var confirmMessage = 'Are you sure you want to delete ' + selectedItems.length + ' account(s)?';
            if (!confirm(confirmMessage)) {
                $(this).val('-1');
                return false;
            }
        }
    });

    // Dashboard stats animation
    $(window).on('load', function() {
        $('.aea-stat-number').each(function() {
            var $this = $(this);
            var countTo = parseInt($this.text());
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'linear',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        });
    });

})(jQuery);
