<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { usePermissions } from '../../Composables/usePermissions';
import { useRoute } from '../../Utils/route';

const urls = usePage().props.urls ?? {};
const route = useRoute();
const { can } = usePermissions();
</script>

<template>
    <div class="topnav">
        <div class="container-fluid">
            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
                <div id="topnav-menu-content" class="collapse navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <Link class="nav-link pr-1" :href="route('inertia.dashboard')">
                                <i class="fas fa-home"></i> Dashboards
                            </Link>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-users-cog"></i> Party Management <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.parties.index')"><b><i class="fas fa-users"></i> Parties</b></Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.customers.index')"><i class="fas fa-user-tie"></i> Customers</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.vendors.index')"><i class="fas fa-address-card"></i> Vendors</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.conduct-persons.index')"><i class="fas fa-user-friends"></i> Contact Persons</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.brokers.index')"><i class="fas fa-money-check-alt"></i> Brokers</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.party-balances.index')"><i class="fas fa-money-check"></i> Party Balance</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.broker-balances.index')"><i class="fas fa-money-check"></i> Broker Balance</Link>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-pallet"></i> FarmManagement <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-warehouse"></i> Farms <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <Link class="dropdown-item" :href="route('inertia.personal-farms.index')">Personal Farms</Link>
                                        <Link class="dropdown-item" :href="route('inertia.customer-farms.index')">Customer Farms</Link>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.product-stores.index')"><i class="fas fa-store-alt"></i> Store Mangement</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.employees.index')"><i class="fas fa-id-card"></i> Employee</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.products.index')"><i class="fas fa-store"></i> Products</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.product-purchases.index')"><i class="fas fa-shopping-bag"></i> Product Purchases</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.product-sales.index')"><i class="fas fa-shopping-bag"></i> Product Sales</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.vaccinations.index')"><i class="fas fa-syringe"></i> Vaccination</Link>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-hotel"></i> <i class="ti ti-buildings"></i> Companies <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.companies.index')"><i class="ti ti-list"></i> Companies</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.company-balances.index')"><i class="fe-bookmark"></i> Companies Balance</Link>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <Link class="nav-link" :href="route('inertia.expenses.index')"><i class="fas fa-wallet"></i> Expenses</Link>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-layer-group"></i> Inventory <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-list-ul"></i> Chicks<div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <Link class="dropdown-item" :href="route('inertia.chick-sales.index')"><i class="fas fa-paper-plane"></i> Sales</Link>
                                        <Link class="dropdown-item" :href="route('inertia.chick-purchases.index')"><i class="fas fa-credit-card"></i> Purchase</Link>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.feeds.index')"><i class="fas fa-braille"></i> Feed</Link>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i> Payments <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <Link class="dropdown-item" :href="route('inertia.payables.index')"><i class="fas fa-credit-card"></i> Account Payable</Link>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-file-alt"></i> Reports <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-bird"></i> Chicks <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <Link class="dropdown-item" :href="route('inertia.reports.chick-sale')">Sale</Link>
                                        <Link class="dropdown-item" :href="route('inertia.reports.chick-purchase')">Purchase</Link>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fe-file"></i> Products <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <Link class="dropdown-item" :href="route('inertia.reports.product-purchase')">Product Purchase</Link>
                                        <Link class="dropdown-item" :href="route('inertia.reports.product-sale')">Product Sale</Link>
                                        <Link class="dropdown-item" :href="route('inertia.reports.product')">Products</Link>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-layer-group"></i> User Management <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu">
                                <div v-if="can('users.viewAny')" class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.users.index')"><i class="fa fa-users"></i> Users</Link>
                                </div>
                                <div class="dropdown">
                                    <Link class="dropdown-item" :href="route('inertia.user-roles.index')"><i class="fa fa-users"></i> User Roles</Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</template>
