<template >
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">Task Manager</span>

            <VDropdown
            :distance="5"
            :skidding="-60"
            :arrow-padding="24"


            >

                <span
                    class="d-flex align-items-center gap-2 text-decoration-none"
                    href="#"
                    role="button"
                    id="userDropdown"
                    aria-expanded="false"
                >
                    <img src="https://i.pravatar.cc/30" class="rounded-circle shadow" alt="User" width="30" height="30" />
                </span>

                <!-- This will be the content of the popover -->

                <template #popper>
                    <ul class="dropdown-menu show position-static m-0 shadow-sm">
                        <li>
                          <router-link class="dropdown-item" to="/logout">Logout</router-link>
                        </li>
                        <li><router-link class="dropdown-item" to="/about">About</router-link></li>

                        <!-- Submenu -->
                        <Menu placement="right-start" :triggers="['hover']">
                            <li
                                class="dropdown-item d-flex justify-content-between align-items-center"
                                role="button"
                            >
                                Services <span>&raquo;</span>
                            </li>
                            <template #popper>
                                <ul class="dropdown-menu show position-static m-0">

                                    <li><a class="dropdown-item" href="#">Mobile Apps</a></li>
                                </ul>
                            </template>
                        </Menu>
                        <li>
                            <router-link class="dropdown-item" to="/profile">Profile</router-link>
                        </li>
                        <li><a class="dropdown-item" href="#">Contact</a></li>
                    </ul>
                </template>
            </VDropdown>
        </div>
    </nav>
</template>
<script setup>
</script>
<style scoped lang="scss">
.dropdown-menu {
    display: block; /* Always shown manually with floating-vue */
    min-width: 180px;
}
</style>
