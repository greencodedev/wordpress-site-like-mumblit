<?php><b><i class="fa fa-sitemap"></i></b>

                            <span style="color:green;"><?php echo Wo_CountUserViews($wo['user']['user_id']);?></span> people reached <i class="fa fa-question-circle" title="Total number of views of all your Mumbls." data-toggle="tooltip"></i><br><span style="color: green; font-size: 12px; margin-left: 23px;">+<?php echo Wo_CountUserNewViews($wo['user']['user_id']);?> reach this week! <i class="fa fa-question-circle" style="color: #462178;" title="Your weekly reach (Mon-Sun)" data-toggle="tooltip"></i></span> 
<hr style=" border: 0;
    border-bottom: 1px dashed #ccc;
    background: #999;">
                      

                     

                            <b><i class="fa fa-share"></i></b>

                            <span style="color: green;"><?php echo Wo_CountUserShares($wo['user']['user_id']);?></span> total shares <i class="fa fa-question-circle" title="Total number of shares all of your Mumbls have accrued." data-toggle="tooltip"></i>
							<?php>