{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-external-link-square"></i>
                            Bug to be Fixed
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                                </a>
                            </div>
                        </div>
                        <div class="panel-body buttons-widget">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-center">Priority</th>
                                        <th class="text-center">Submitted By</th>
                                        <th class="text-center">Submitted At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% for item in bug %}
                                        <tr>
                                            <td>{{ item.description }}</td>
                                            <td class="text-center" width="100">{{ item.btp_name }}</td>
                                            <td class="text-center" width="220">{{ item.full_name }}</td>
                                            <td class="text-center" width="150">{{ item.b_created_at }}</td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-external-link-square"></i>
                            Feature Request
                            <div class="panel-tools">
                                <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                                </a>
                            </div>
                        </div>
                        <div class="panel-body buttons-widget">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="sample-table-1">
                                    <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-center">Priority</th>
                                        <th class="text-center">Submitted By</th>
                                        <th class="text-center">Submitted At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {% for item in request %}
                                        <tr>
                                            <td>{{ item.description }}</td>
                                            <td class="text-center" width="100">{{ item.btp_name }}</td>
                                            <td class="text-center" width="220">{{ item.full_name }}</td>
                                            <td class="text-center" width="150">{{ item.b_created_at }}</td>
                                        </tr>
                                    {% endfor %}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
