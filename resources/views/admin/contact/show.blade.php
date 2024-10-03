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
                                            country
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->country }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            address
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            state
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->state }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            pincode
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->pincode }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            subject
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>
                                            message
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $result->message }}
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
