{% if item is defined %}
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        <h4 class="modal-title" id="myLargeModalLabel">{{ item['btt_name'] }}</h4>
    </div>
    <div class="modal-body">
        <table class="table table-bordered table-hover dataTable table-bug-tracking">
            <tr>
                <td><strong>Description</strong></td>
                <td>
                    {{ item['description'] }}
                </td>
            </tr>
            {% if item['image'] != '' %}
                <tr>
                    <td><strong>Image</strong></td>
                    <td><img src="{{ item['image'] }}?time={{ time() }}" style="max-width: 300px"></td>
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
                    <button class="btn btn-sm btn-success" id="z-change-status-tracking" data-content="{{ item['bid'] }}">Change Status</button> or
                    <a style="color: #fff; text-decoration: none" target="_blank" href="{{ linkDetail }}{{ item['bid'] }}"><button class="btn btn-sm btn-warning">View Full Page</button></a>
                    {% if accessEdit is defined %}
                        or <a style="color: #fff; text-decoration: none" target="_blank" href="/admin/bugtracking/index/edit/{{ item['bid'] }}"><button class="btn btn-sm btn-warning">Edit</button></a>
                    {% endif %}
                </td>
            </tr>
        </table>
    </div>
{% else %}
{% endif %}