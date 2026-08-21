<script setup>
import PageTitle from '../Layouts/Partials/PageTitle.vue';
</script>

<template>
    <div class="container-fluid">
        <PageTitle title="Dashboard" :crumbs="['Home']" />

        <div class="row">
            <div class="col-md-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body ps-2 py-2">
                        <div class="row">
                            <div class="col-2">
                                <div class="mt-2">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-10">
                                <div class="text-left" style="margin-left:10px;">
                                    <p class="text-muted mb-1">Purchase</p>
                                    <h5 class="text-dark mt-1"><span> 5000 </span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body ps-2 py-2">
                        <div class="row">
                            <div class="col-2">
                                <div class="mt-2">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-10">
                                <div class="text-left" style="margin-left:10px;">
                                    <p class="text-muted mb-1">Sale</p>
                                    <h5 class="text-dark mt-1"><span> 3000 </span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body ps-2 py-2">
                        <div class="row">
                            <div class="col-2">
                                <div class="mt-2">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-10">
                                <div class="text-left" style="margin-left:10px;">
                                    <p class="text-muted mb-1">Total Shed </p>
                                    <h5 class="text-dark mt-1"><span> 100 </span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget-rounded-circle card">
                    <div class="card-body ps-2 py-2">
                        <div class="row">
                            <div class="col-2">
                                <div class="mt-2">
                                    <i class="fas fa-university fa-2x"></i>
                                </div>
                            </div>
                            <div class="col-10">
                                <div class="text-left" style="margin-left:10px;">
                                    <p class="text-muted mb-1">Total Clients</p>
                                    <h5 class="text-dark mt-1"><span> 50 </span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
