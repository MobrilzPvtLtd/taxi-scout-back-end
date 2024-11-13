@extends('admin.layouts.app')

@section('title', 'Contact Enquery')

@section('content')

    <section class="content">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="align-self-center">
                        <h4 class="card-title mb-0">
                            <i class="fa-regular fa-sun"></i> Contact <small class="text-muted">Show</small>
                        </h4>
                    </div>
                    <div class="btn-toolbar d-block text-end" role="toolbar" aria-label="Toolbar with buttons">
                        <a href="{{ url('contact') }}" class="btn btn-secondary btn-sm" data-toggle="tooltip"
                            data-coreui-original-title="Hotels List"><i class="fa fa-list"></i> List
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-responsive-sm table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <strong> Name </strong>
                                    </th>
                                    <th scope="col">
                                        <strong> Value </strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>
                                            Id
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Name
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Email
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Mobile
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->mobile }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Country
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->country }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Address
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            State
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->state }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Pincode
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->pincode }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Subject
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Message
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->message }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Status
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="dropdown-toggle text-white btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="{{ $result->status == 'open' ? 'background-color: #fc4b6c;' : 'background-color: #008000;' }} border: none; color:#fff">
                                                {{ $result->status == 'open' ? 'Open' : 'Close' }}
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form action="{{ url('contact/is_view') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="contactId" id="" value="{{ $result->id }}">
                                                        <input type="hidden" name="status" id="" value="{{ $result->status == 'open' ? 'close' : 'open' }}">
                                                        <button type="submit" class="dropdown-item" style="cursor: pointer;">
                                                            {{ $result->status == 'open' ? 'Close' : 'Open' }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            Created At
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
