@extends('web::layouts.grids.6-6')

@section('title', trans('smfbridge::seat.settings'))
@section('page_header', trans('smfbridge::seat.settings'))

@section('left')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Configuration</h3>
        </div>
        <div class="panel-body">
            <form role="form" action="" method="post" class="form-horizontal">
                {{ csrf_field() }}

                <div class="box-body">

                    <legend>Simple Machine Forum Bridge</legend>

                    <p class="callout callout-warning text-justify">To use this bridge, you must know <code>the full path to your SMF install</code></p>

                    <div class="form-group">
                        <label for="slack-configuration-client" class="col-md-4">Full Path to SMF Install</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control " id="slack-configuration-client"
                                       name="slack-configuration-client" value="" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slack-configuration-secret" class="col-md-4">Slack Client Secret</label>
                        <div class="col-md-7">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="slack-configuration-secret"
                                       name="slack-configuration-secret" />
                            </div>
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">Update</button>
                </div>

            </form>
        </div>
    </div>
@stop

@section('right')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-rss"></i> Update feed</h3>
        </div>
        <div class="panel-body" style="height: 500px; overflow-y: scroll">
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-6">
                    Installed version: <b></b>
                </div>
                <div class="col-md-6">
                    Latest version:
                    <a href="https://packagist.org/packages/warlof/slackbot">
                        <img src="https://poser.pugx.org/warlof/slackbot/v/stable" alt="Slackbot version" />
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
