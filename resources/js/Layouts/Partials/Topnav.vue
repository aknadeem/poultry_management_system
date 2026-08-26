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
                                    <a class="dropdown-item" :href="urls.productSales"><i class="fas fa-shopping-bag"></i> Product Sales</a>
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
                                    <a class="dropdown-item" :href="urls.companies"><i class="ti ti-list"></i> Companies</a>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item" :href="urls.companyBalance"><i class="fe-bookmark"></i> Companies Balance</a>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" :href="urls.expenses"><i class="fas fa-wallet"></i> Expenses</a>
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
                                        <a class="dropdown-item" :href="urls.chickSales"><i class="fas fa-paper-plane"></i> Sales</a>
                                        <a class="dropdown-item" :href="urls.chickPurchases"><i class="fas fa-credit-card"></i> Purchase</a>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item" :href="urls.feed"><i class="fas fa-braille"></i> Feed</a>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-list"></i> Payments <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" :href="urls.payables"><i class="fas fa-credit-card"></i> Account Payable</a>
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
                                        <a class="dropdown-item" :href="urls.chickSaleReport">Sale</a>
                                        <a class="dropdown-item" :href="urls.chickPurchaseReport">Purchase</a>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a class="dropdown-item dropdown-toggle arrow-none" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fe-file"></i> Products <div class="arrow-down"></div>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" :href="urls.productPurchaseReport">Product Purchase</a>
                                        <a class="dropdown-item" :href="urls.productSaleReport">Product Sale</a>
                                        <a class="dropdown-item" :href="urls.productReport">Products</a>
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
