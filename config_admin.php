<?php
  require 'login_protection.php';
  require 'includes/db_connection.php';
?>
<html>

  <head>
    <title>PHPhonebook</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-table.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-editable.css">
    <link rel="stylesheet" type="text/css" href="css/material.min.css">
    <link rel="stylesheet" type="text/css" href="css/material-wfont.min.css">
    <link rel="stylesheet" type="text/css" href="css/ripples.min.css">
    <link rel="stylesheet" type="text/css" href="css/custom-admin.css">
  </head>

  <body>
    <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#nc-modal">
      New Contact
    </button>
    <button id="edit-btn" class="btn btn-primary pull-left">Edit</button>
    <div class="modal fade" id="nc-modal" tabindex="-1" role="dialog" aria-labelledby="nc-label" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title" id="nc-label">New Contact</h4>
          </div>
          <div class="modal-body">
            <div id="msg" class="alert hide"></div>
            <form id="new-form">
              <fieldset>
                <div class="form-group">
                  <h2 class="fs-title">Contact Details</h2>

                  <label for="icon">Distro Icon</label>
                  <input type="text" class="form-control" name="icon" required placeholder="Icon">

                  <label for="label">Distro Label</label>
                  <input type="text" class="form-control" name="dname" required placeholder="Distro">

                </div>
              </fieldset>
            </form>
            <div class="modal-footer">
              <button type="reset"  id="close-btn" class="btn btn-default" data-dismiss="modal" form="new-form">Close</button>
              <button type="submit" id="save-btn"  class="btn btn-primary" form="new-form">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable" id="datatable">
      <thead>
        <tr>
          <th colspan="1" rowspan="1" style="width: 180px;" tabindex="0">Icon</th>
          <th colspan="1" rowspan="1" style="width: 220px;" tabindex="0">Distro</th>
        </tr>
      </thead>
      <tbody id="contact-table">
        <?php
          $sql = "select * from contacts";
          $results = $db->query($sql);
          $i=0;
          foreach($results as $row){
            $class = ( $i%2==0 ? 'even' : 'odd');
            echo sprintf('<tr rowid="%s" class="%s">', $row["id"], $class);
            echo sprintf('<td><span data-name="dname" data-pk="%s" class="xedit dname">%s</span></td>', $row["id"], $row["dname"]);
            echo sprintf('<td><span data-name="icon"  data-pk="%s" class="xedit icon ">%s</span></td>', $row["id"], $row["lname"]);
            echo sprintf('<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>');
            echo sprintf('</tr>');
            $i++;
          }
        ?>
      </tbody>
    </table>

    <script src="js/jquery-2.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-table.js"></script>
    <script src="js/bootstrap-editable.min.js"></script>
    <script src="js/ripples.min.js"></script>
    <script src="js/material.min.js"></script>

    <script>
      $(document).ready(function(){
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.disabled = true;
        // $.fn.editable.defaults.showbuttons = false;
        setEvents();
        $('.xedit, .new-field').editable({
          url: './backend.php',
          validate: function(val){
            console.log(val);
            if (!val) return 'Required field';
          }
        });
        $('.new-field').editable('enable');
        $('#edit-btn').click(function(){
          $('.xedit').not('.new-field').editable('toggleDisabled');
          $('.delete-field').toggle();
          $(this).toggleClass('btn-primary').toggleClass('btn-success');
          $(this).text(function(i, text){
            return text === 'Edit' ? 'Done' : 'Edit';
          });
        });
        $('#close-btn').click(function() {
          $('.new-field').editable('setValue', null)
                         .removeClass('editable-unsaved');
        });
        $('#new-form').submit(function(e) {
          $('#nc-modal').modal('toggle');
          e.preventDefault();
          $.ajax({
            type: "POST",
            url: "./backend.php",
            data: $(this).serialize(),
            dataType: "json",
            success: createRow
          });
        });
      });
      function createRow(data){
        if (data && data.id) { //record created, response like {"id": 2}
          var new_row = $('<tr rowid="'+data.id+'">' +
            '<td><span data-name="fname" data-pk="'+data.id+'" class="xedit fname">'+data.fname+'</span></td>' +
            '<td><span data-name="lname" data-pk="'+data.id+'" class="xedit lname">'+data.lname+'</span></td>' +
            '<td><span data-name="phone" data-pk="'+data.id+'" data-type="tel" class="xedit phone">'+data.phone+'</span></td>' +
            '<td class="delete-field"><button class="btn btn-danger delete-btn">Delete</button></td>' +
          '</tr>');

          //add the new row to the table
          $('#contact-table').append(new_row);

          //activate events
          setEvents();

          //remove unsaved class
          $('.new-field').editable('setValue', null);
          $('.new-field').removeClass('editable-unsaved');

        } else {
          alert('Error adding row');
        }
      }
      function setEvents(){
        $('.delete-field').hide();
        $('.xedit').editable({
          url: './backend.php',
          validate: function(val){
            console.log(val);
            if (!val) return 'Required field';
          }
        });
        $('.delete-btn').click(function(){
          var row = $(this).closest('tr');
          $.ajax({
            type: 'POST',
            url: './backend.php',
            dataType: 'JSON',
            data: {'delete_pk': row.attr('rowid')}
          });
          row.remove();
        });
        $('#phone, .phone').editable('option', 'validate', function(val){
          regex = /^\(*?\d{3}\)*?[-\. ]*?\d{3}[-\. ]*?\d{4}$/;
          if (!regex.test(val)) return 'Invalid Phone Number';
        });

        $.material.init();
      }
    </script>
  </body>

</html>
