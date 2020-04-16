<?php
/* @var $this View */
/* @var $content string */

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

AppAsset::register($this);
View::registerCssFile(yii::$app->urlManager->createUrl('js/jstree/dist/themes/default/style.css'));
View::registerJsFile(yii::$app->urlManager->createUrl('js/jstree/dist/jstree.min.js'), ['depends' => JqueryAsset::className()]);
View::registerJsFile(yii::$app->urlManager->createUrl('js/organisation.js'), ['depends' => JqueryAsset::className()]);
?>
<div class="container-fluid">
    <h2>Lookup Options</h2>
</div>                
<div class="wa-notification wa-notification-alt">
    <span class="wa-iconBoxed">
        <span class="wa-icon wa-icon-notification"></span>
    </span>
    <p id="description-text">
        Adding Locations, Assigning Hotels, Departments and sections can be managed from this screen.
    </p>
</div>

<div class="col-md-12 ">
    <ul class="nav nav-tabs">
        <li class="active"><a class="tabs" data-toggle="tab"><i class="fa fa-map-marker" aria-hidden="true"></i>Hyderabad</a></li>
        <li><a class="tabs" data-toggle="modal" data-target="#addlocations"><i class="fa fa-plus-square" aria-hidden="true"></i>Add Location</a></li>
    </ul>

</div>
<div class="col-sm-12 margintop10" >
    <div class="col-sm-6" style="height: 30%; overflow-y: scroll;">
        <div class="col-sm-12">

        </div>
        <div class="col-sm-12">
            <h4>Manage Hotels & Departments </h4>
            <div class=" col-sm-6">
                <div id="tree_25" class="">
                    <ul class="">
                        <li class="jstree-open">
                            <label id="editlocation" class="changeEditdiv">Hyderabad</label>
                            <ul>
                                <li class="jstree-open">
                                    <label id="edithotel" class="changeEditdiv">GreenPark - Ameerpet</label>
                                    <ul >
                                        <li class="jstree-open">
                                            <label id="editdepartment" class="changeEditdiv">Engineering</label>
                                            <ul>
                                                <li class="jstree-open">
                                                    <label id="editsection" class="changeEditdiv">Preventive Maintenance Schedule</label>
                                                    <ul>
                                                        <li id="editsubsection" class="changeEditdiv">Boilers</li>
                                                        <li>AC</li>
                                                    </ul>
                                                </li>              
                                                <li>Performance Of Machinaries</li>
                                                <li>Annual Maintenance Contracts</li>
                                                <li>Monthly Information System</li>
                                                <li>Heat Light, R&M Expenditure</li>
                                                <li>Ken Fixit Rooms</li>
                                                <li>Capex / Rennovations</li>
                                            </ul>
                                        </li>
                                        <li>Finance</li>
                                        <li>Human Resource</li>
                                        <li>F&B</li>
                                        <li>House Keeping</li>
                                        <li>Security</li>
                                        <li>Front Desk</li>
                                        <li>Administration</li>
                                    </ul>
                                </li>
                                <li class="jstree">
                                    <label>AVASA - HiTech</label>
                                    <ul>
                                        <li>Engineering</li>
                                        <li>Finance</li>
                                        <li>Human Resource</li>
                                        <li>F&B</li>
                                        <li>House Keeping</li>
                                        <li>Security</li>
                                        <li>Front Desk</li>
                                        <li>Administration</li>

                                    </ul>
                                </li>                                                      
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div id="editlocationdiv" class="showhidediv">
            <form id="" name="editlocation" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Edit Location</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="addhotel" style="border: 1px solid #ccc" class="btn btn-md changediv pull-right">Add Hotel</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Location" value="Hyderabad" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea rows="6" class="form-control" placeholder="Description" id="description" name="description">Comfortable rooms, superior facilities to conduct your business and wonderful spreads lined up to suit your palate, GreenPark Hyderabad is optimized to your world of needs. Located at the heart of Hyderabad’s commercial business district, GreenPark Hyderabad makes an ideal stopover for business travellers.</textarea>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="addhoteldiv" class="showhidediv" style="display: none;">
            <form id="" name="addhotel" method="post" >
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Add Hotel</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="adddepartment" class="btn btn-md changediv pull-right" style="border: 1px solid #ccc">Add Department</p>
                    </div>
                </div>                                  
                <div class="row" >

                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="" placeholder="Contact" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>      
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="adddepartmentdiv" class="showhidediv" style="display: none;">
            <form id="" name="adddepartment" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Add Department</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="addsection" class="btn btn-md changediv pull-right"  style="border: 1px solid #ccc" >Add Section</p>
                    </div>
                </div>
                <div class="row" >                                                     
                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>                                        
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly="">HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class=" col-sm-10">
                                <select class="form-control" name="departments[]" id="departmentsmultiple" multiple="multiple">
                                    <option>select Department</option>
                                    <option value="Engineering">Engineering</option>
                                    <option value="Finance">Finance</option>
                                    <option value="IT">IT</option>
                                </select>

                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>                                            
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="addsectiondiv" class="showhidediv"  style="display: none;">
            <form id="" name="section" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Add Section</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="addsubsection" class="btn btn-md changediv pull-right"  style="border: 1px solid #ccc" >Add Subsection</p>
                    </div>
                </div>
                <div class="row" >                                                     
                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>                                        
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly="">HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class=" col-sm-10">
                                <input type="text" name="department" class="form-control" placeholder="Department" value="Engineering" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value="" readonly>The engineering discipline concerned with the machinery and systems of ships and other marine vehicles and structures.</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Section :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Section" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="addsubsectiondiv" class="showhidediv" style="display: none;">
            <form id="" name="section" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Add Subsection</h4>
                    </div>                                
                </div>
                <div class="row" >                                                     
                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>                                        
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly="">HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class=" col-sm-10">
                                <input type="text" name="department" class="form-control" placeholder="Department" value="Engineering" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value="The engineering discipline concerned with the machinery and systems of ships and other marine vehicles and structures." readonly>The engineering discipline concerned with the machinery and systems of ships and other marine vehicles and structures.</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Section :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Section" value="Preventive Maintenance Schedule"  readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Subsection :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="SubSection" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="edithoteldiv" class="showhidediv" style="display: none;">
            <form id="" name="addhotel" method="post" >
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Edit Hotel</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="adddepartment" class="btn btn-md changediv pull-right" style="border: 1px solid #ccc">Add Department</p>
                    </div>
                </div>
                <div class="row" >

                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                            <a title="Delete" data-toggle="modal" data-target="#deletepopup" class="btn btn-danger pull-right">Delete</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="editdepartmentdiv" class="showhidediv" style="display: none;">
            <form id="" name="editdepartmentdiv" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Edit Department</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="addsection" class="btn btn-md changediv pull-right"  style="border: 1px solid #ccc" >Add Section</p>
                    </div>
                </div>
                <div class="row" >

                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly>HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10">
                                <input type="text" name="department" class="form-control" placeholder="Department" value="Engineering">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value=""></textarea>
                            </div>
                        </div>
                    </div>

                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                            <a title="Delete" data-toggle="modal" data-target="#deletepopup" class="btn btn-danger pull-right">Delete</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="editsectiondiv" class="showhidediv" style="display: none;">
            <form id="" name="editsectiondiv" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Edit Section</h4>
                    </div>
                    <div class="col-md-6" >
                        <p id="addsubsection" class="btn btn-md changediv pull-right"  style="border: 1px solid #ccc" >Add Subsection</p>
                    </div>
                </div>

                <div class="row" >

                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly="">HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class=" col-sm-10">
                                <input type="text" name="department" class="form-control" placeholder="Department" value="Engineering" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value="" readonly>The engineering discipline concerned with the machinery and systems of ships and other marine vehicles and structures.</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Section :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Section" value="Preventive Maintenance Schedule"  />
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                            <a title="Delete" data-toggle="modal" data-target="#deletepopup" class="btn btn-danger pull-right">Delete</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
        <div id="editsubsectiondiv" class="showhidediv" style="display: none;">
            <form id="" name="section" method="post">
                <div class="row">
                    <div class="col-md-6 ">
                        <h4>Edit Subsection</h4>
                    </div>
                </div>

                <!-- <div class="col-md-6" >
                       <p id="subsection" class="btn btn-md changediv pull-right"  style="border: 1px solid #ccc" >Add Subsection</p>
                    </div> -->
                <div class="row" >                                                     
                    <div class="col-sm-12">
                        <div class="col-sm-12 ">
                            <label>Location :</label>
                        </div>                                        
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="Hyderabad" placeholder="Location" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Hotel Name :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Hotel Name" value="Green Park" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Contact :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" value="998877665" placeholder="Contact" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Address :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="address" class="form-control" id="address" placeholder="Address" value="HiTech City" readonly="">HiTech City</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Department :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class=" col-sm-10">
                                <input type="text" name="department" class="form-control" placeholder="Department" value="Engineering" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <textarea name="description" class="form-control" id="description" placeholder="Description" value="" readonly>The engineering discipline concerned with the machinery and systems of ships and other marine vehicles and structures.</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Section :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="Section" value="Preventive Maintenance Schedule" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-12 ">
                            <label>Subsection :</label>
                        </div>
                        <div class="col-sm-12 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" placeholder="SubSection" value=""  />
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">                                       
                        <div class="col-sm-12  pull-right">
                            <a class="btn btn-success">Update</a>
                            <a class="btn btn-default">Close</a>
                            <a title="Delete" data-toggle="modal" data-target="#deletepopup" class="btn btn-danger pull-right">Delete</a>
                        </div>                    
                    </div>
                </div>
            </form>
        </div>
    </div>                       

</div>
<!-- add locations modal -->
<div id="addlocations" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Locations</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-3 ">
                            <label>Location :</label>
                        </div>
                        <div class="col-sm-9 ">
                            <div class="col-sm-10 ">
                                <input type="text" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" style="margin-top: 20px;">
                        <div class="col-sm-3 ">
                            <label>Description :</label>
                        </div>
                        <div class="col-sm-9 ">
                            <div class="col-sm-10 ">
                                <textarea class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-12' style="margin-top: 20px;">
                        <div class="col-sm-3 ">                                          
                        </div>
                        <div class="col-sm-9 ">
                            <div class="col-sm-10">
                                <a class="btn btn-success" data-dismiss="modal">Save</a>
                                <a class="btn btn-default" data-dismiss="modal">Close</a>
                            </div>
                        </div>

                        <!--  <div class="col-sm-8">
                         <div class="col-sm-3 ">
                             <label>Enter OTP :</label>
                         </div>
                         <div class="col-sm-6 ">
                             <div class="col-sm-10 ">
                                 <input type="text" class="form-control" />
                             </div>
                         </div> 
                         <a href="#">Generate OTP</a> 
                         </div>    -->                              
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- delete opup modal start -->
<div id="deletepopup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff!important; opacity: 1;" aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body clearfix">
                <div class="col-sm-12" style="margin-top: 20px;">
                    <label>
                        Are you sure you want to delete this record? You can't undo this action.
                    </label>
                </div>
            </div>
            <div class="modal-footer clearfix" style="border-top: none; margin-top: 5px;">
                <div class="col-sm-12">
                    <input id="delete" class="btn btn-success" onclick="toastr.success('Deleted Successfully');" type="submit" name="yt0" value="Delete">
                    <button type="button" class="btn btn-Clear" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>