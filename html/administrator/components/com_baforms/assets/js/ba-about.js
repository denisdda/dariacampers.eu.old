/**
* @package   BaForms
* @author    Balbooa http://www.balbooa.com/
* @copyright Copyright @ Balbooa
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

function showNotice(message, className)
{
    if (!className) {
        className = '';
    }
    if (notification.hasClass('notification-in')) {
        setTimeout(function(){
            notification.removeClass('notification-in').addClass('animation-out');
            setTimeout(function(){
                addNoticeText(message, className);
            }, 400);
        }, 2000);
    } else {
        addNoticeText(message, className);
    }
}

function addNoticeText(message, className)
{
    var time = 3000;
    if (className) {
        time = 6000;
    }
    notification.find('p').html(message);
    notification.addClass(className).removeClass('animation-out').addClass('notification-in');
    setTimeout(function(){
        notification.removeClass('notification-in').addClass('animation-out');
        setTimeout(function(){
            notification.removeClass(className);
        }, 400);
    }, time);
}

function getUserLicense(data)
{
    $f.ajax({
        type:"POST",
        dataType:'text',
        url:"index.php?option=com_baforms&task=forms.getUserLicense",
        data:{
            data: data
        },
        success : function(msg){
            if (uploadMode != 'updateForms') {
                showNotice(formsLanguage['YOUR_LICENSE_ACTIVE']);
            }
            $f('#toolbar-about span[data-notification]').each(function(){
                this.dataset.notification = this.dataset.notification * 1 - 1;
            });
            $f('.forms-activate-license').hide();
            $f('.forms-deactivate-license').css('display', '');
        }
    });
}

function listenMessage(event)
{
    if (event.origin == 'https://www.balbooa.com') {
        try {
            let obj = JSON.parse(event.data);
            getUserLicense(obj.data);
            if (uploadMode == 'updateForms') {
                updateForms(formsApi.package);
            }
        } catch (error) {
            showNotice(event.data, 'ba-alert');
        }
        jQuery('#login-modal').modal('hide');
    }
}

function updateForms(package)
{
    setTimeout(function(){
        var str = formsLanguage['UPDATING']+'<img src="'+JUri;
        str += 'components/com_baforms/assets/images/reload.svg"></img>';
        notification[0].className = 'notification-in';
        notification.find('p').html(str);
    }, 400);
    var XHR = new XMLHttpRequest(),
        url = 'index.php?option=com_baforms&task=forms.updateForms&tmpl=component',
        data = {
            method: window.atob('YmFzZTY0X2RlY29kZQ=='),
            package: package
        };
    XHR.onreadystatechange = function(e) {
        if (XHR.readyState == 4) {
            setTimeout(function(){
                notification[0].className = 'animation-out';
                setTimeout(function(){
                    notification.find('p').html(formsLanguage['UPDATED']);
                    notification[0].className = 'notification-in';
                    setTimeout(function(){
                        notification[0].className = 'animation-out';
                        setTimeout(function(){
                            window.location.href = window.location.href;
                        }, 400);
                    }, 3000);
                }, 400);
            }, 2000);
        }
    };
    XHR.open("POST", url, true);
    XHR.send(JSON.stringify(data));
}

var $f = jQuery,
    notification = uploadMode = null;

document.addEventListener('DOMContentLoaded', function(){
    notification = jQuery('#ba-notification');
    
    $f('.ba-dashboard-apps-dialog').on('click', function(event){
        event.stopPropagation();
    });
    $f('body').on('click', function(event){
        $f('.ba-dashboard-apps-dialog.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
    });
    $f('body').on('click', '.ba-dashboard-popover-trigger', function(event){
        event.stopPropagation();
        let div = document.querySelector('.'+this.dataset.target),
            rect = this.getBoundingClientRect();
        div.classList.add('visible-dashboard-dialog');
        let left = (rect.left - div.offsetWidth / 2 + rect.width / 2),
            arrow = '50%';
        if (this.dataset.target == 'blog-settings-context-menu' && left < 110) {
            left = 110;
            arrow = (rect.left - 110 + rect.width / 2)+'px'
        }
        div.style.setProperty('--arrow-position', arrow);
        div.style.top = (rect.bottom + window.pageYOffset + 10)+'px';
        div.style.left = left+'px';
    });
    $f('#toolbar-language button').on('click', function(){
        $f('#language-dialog').modal();
    });
    $f('.ba-import-form').on('click', function(){
        $f('#upload-dialog').modal();
    });
    $f('#toolbar-language button').on('click', function(){
        $f('#language-dialog').modal();
    });
    $f('.languages-wrapper').on('click', '.language-title', function(){
        var str = formsLanguage['INSTALLING']+'<img src="'+JUri;
        str += 'components/com_baforms/assets/images/reload.svg"></img>';
        notification.addClass('notification-in');
        notification.find('p').html(str);
        $f('#language-dialog').modal('hide');
        $f.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_baforms&task=forms.addLanguage",
            data:{
                method: window.atob('YmFzZTY0X2RlY29kZQ=='),
                url: formsApi.languages[this.dataset.key].url,
                zip: formsApi.languages[this.dataset.key].zip,
            },
            success: function(msg){
                showNotice(msg)
            }
        });
    });
    $f('#apply-deactivate').on('click', function(event){
        event.preventDefault();
        $f.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_baforms&task=forms.checkFormsState",
            success: function(msg){
                var obj = JSON.parse(msg),
                    url = 'https://www.balbooa.com/demo/index.php?',
                    script = document.createElement('script');
                url += 'option=com_baupdater&task=baforms.deactivateLicense';
                url += '&data='+obj.data;
                url += '&time='+(+(new Date()));
                script.onload = function(){
                    $f.ajax({
                        type : "POST",
                        dataType : 'text',
                        url : JUri+"index.php?option=com_baforms&task=form.setAppLicense",
                        success: function(msg){
                            showNotice(formsLanguage['SUCCESSFULY_DEACTIVATED']);
                            $f('#toolbar-about span[data-notification]').each(function(){
                                this.dataset.notification = this.dataset.notification * 1 + 1;
                            });
                            $f('.forms-activate-license').css('display', '');
                            $f('.forms-deactivate-license').hide();
                        }
                    });
                }
                script.src = url;
                document.head.appendChild(script);
            }
        });
        $f('#deactivate-dialog').modal('hide');
    });
    $f('.activate-link').on('click', function(event){
        event.preventDefault();
        $f('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        uploadMode = 'activateForms';
        $f('#login-modal').modal();
    });
    $f('.deactivate-link').on('click', function(event){
        event.preventDefault();
        $f('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        $f('#deactivate-dialog').modal();
    });
    $f('.forms-update-wrapper').on('click', '.update-link', function(event){
        event.preventDefault();
        $f('.ba-dashboard-about.visible-dashboard-dialog').removeClass('visible-dashboard-dialog');
        $f.ajax({
            type:"POST",
            dataType:'text',
            url:"index.php?option=com_baforms&task=forms.checkFormsState",
            success: function(msg){
                var flag = true,
                    obj;
                if (msg) {
                    obj = JSON.parse(msg);
                    flag = !obj.data;
                }
                if (flag) {
                    uploadMode = 'updateForms';
                    $f('#login-modal').modal();
                } else {
                    var url = 'https://www.balbooa.com/demo/index.php?',
                        domain = window.location.host.replace('www.', ''),
                        script = document.createElement('script');
                    domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
                    url += 'option=com_baupdater&task=baforms.checkFormsUser';
                    url += '&data='+obj.data;
                    if (domain[domain.length - 1] != '/') {
                        domain += '/';
                    }
                    url += '&domain='+window.btoa(domain);
                    script.onload = function(){
                        if (formsResponse) {
                            updateForms(formsApi.package);
                        } else {
                            uploadMode = 'updateForms';
                            $f('#login-modal').modal();
                        }
                    }
                    script.src = url;
                    document.head.appendChild(script);
                }
            }
        });
    });
    $f('#login-modal').on('show', function(){
        var url = 'https://www.balbooa.com/demo/index.php?option=com_baupdater&view=baforms',
            domain = window.location.host.replace('www.', '');
            iframe = document.createElement('iframe');
        domain += window.location.pathname.replace('index.php', '').replace('/administrator', '');
        if (domain[domain.length - 1] != '/') {
            domain += '/';
        }
        url += '&domain='+window.btoa(domain);
        iframe.onload = function(){
            this.classList.add('iframe-loaded');
        }
        iframe.src = url;
        $f('#login-modal .modal-body').html(iframe);
        window.addEventListener("message", listenMessage, false);
    });
    $f('#login-modal').on('hide', function(){
        window.removeEventListener("message", listenMessage, false);
    });
    $f('.ba-export-form').on('click', function(){
        var exportId = new Array();
        $f('.table-striped tbody input[type="checkbox"]').each(function(){
            if (this.checked) {
                exportId.push(this.value);
            }
        });
        if (exportId.length == 0) {
            $f('#error-dialog').modal();
        } else {
            exportId = exportId.join(';');
            $f.ajax({
                type:"POST",
                dataType:'text',
                url:"index.php?option=com_baforms&view=forms&task=forms.exportForm",
                data:{
                    export_id: exportId,
                },
                success: function(msg){
                    var msg = JSON.parse(msg);
                    if (msg.success) {
                        var iframe = $f('<iframe/>', {
                            name:'download-target',
                            id:'download-target',
                            src:'index.php?option=com_baforms&view=forms&task=forms.download&tmpl=component&file='+msg.message,
                            style:'display:none'
                        });
                        $f('#download-target').remove();
                        $f('body').append(iframe);
                        
                    }
                }
            });
        }
    });
});

document.addEventListener('DOMContentLoaded', function(){
    let script = document.createElement('script');
    script.onload = function(){
        jQuery.ajax({
            type : "POST",
            dataType : 'text',
            url : 'index.php?option=com_baforms&task=forms.versionCompare',
            data : {
                version: formsApi.version
            },
            success: function(msg){
                if (msg == -1) {
                    jQuery('.forms-update-wrapper').each(function(){
                        this.classList.add('forms-update-available');
                        this.querySelector('i').className = 'zmdi zmdi-alert-triangle';
                        this.querySelector('span').textContent = formsLanguage['UPDATE_AVAILABLE'];
                        if (this.classList.contains('forms-update-wrapper')) {
                            let a = document.createElement('a');
                            a.className = 'update-link dashboard-link-action';
                            a.href = "#";
                            a.textContent = formsLanguage['UPDATE'];
                            this.appendChild(a);
                        }
                    });
                    jQuery('.ba-dashboard-popover-trigger[data-target="ba-dashboard-about"]').each(function(){
                        let count = this.querySelector('span[data-notification]');
                        count.dataset.notification = count.dataset.notification * 1 + 1;
                    });
                }
            }
        });
        formsApi.languages.forEach(function(el, ind){
            var str = '<div class="language-line"><span class="language-img"><img src="'+el.flag+'">';
            str += '</span><span class="language-title" data-key="'+ind+'">'+el.title;
            str += '</span><span class="language-code">'+el.code+'</span></div>';
            jQuery('#language-dialog .languages-wrapper').append(str);
        });
    }
    let classList = document.body.classList;
    script.type = 'text/javascript';
    script.src = 'https://www.balbooa.com/updates/baforms/formsApi/formsApi.js';
    document.head.appendChild(script);
});