require([
    'jquery',
    "mage/translate", 
    "mage/adminhtml/events",
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function(jQuery){
    wysiwyg = new wysiwygSetup('spin_rule', {
        'width':'100%',  // defined width of editor
        'height':'300px', // height of editor
        'plugins':[{'name':'image'}], // for image
        'tinymce4':{'toolbar':'formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table charmap','plugins':'advlist autolink lists link charmap media noneditable table contextmenu paste code help table'
        }
    });
    wysiwyg.setup('exact');
});