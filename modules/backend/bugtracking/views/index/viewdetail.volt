{% extends "../../index.volt" %}
{% block content %}
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-external-link-square"></i>
                Detail {{ item['btt_name'] }}
                <div class="panel-tools">
                    <a class="btn btn-xs btn-link panel-collapse collapses" href="#">
                    </a>
                </div>
            </div>
            <div class="panel-body buttons-widget">
                <div class="table-responsive">
                    {% if item is defined %}

                        <table class="table table-bordered table-hover dataTable">
                            <tr>
                                <td><strong>Description</strong></td>
                                <td>{{ item['description'] }}</td>
                            </tr>
                            {% if item['image'] != '' %}
                                <tr>
                                    <td><strong>Image</strong></td>
                                    <td><img src="{{ item['image'] }}" style="max-width: 500px"></td>
                                </tr>
                            {% endif %}
                            <tr>
                                <td><strong>Notify from</strong></td>
                                <td>{{ item['full_name'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Notify to</strong></td>
                                <td>{{ item['name'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created at</strong></td>
                                <td>{{ item['b_created_at'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Priority</strong></td>
                                <td>{{ item['btp_name'] }}</td>
                            </tr>
                            <tr>
                                <td><strong>Change Status</strong></td>
                                <td>{{ bug_tracking_status }}
                                    <br/>
                                    <button class="btn btn-success">Change Status</button>
                                </td>
                            </tr>
                        </table>

                    {% else %}

                    {% endif %}
                </div>
            </div>
        </div>
    </div>
{% endblock %}


