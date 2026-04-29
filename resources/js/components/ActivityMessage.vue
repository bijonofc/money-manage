<template>
  <span v-if="item.type === 'A'">
    <!-- Status change -->
    <template v-if="item.ref_type === 'S'">
      changed task status to
      <span class="text-success fw-medium">
        {{ item.status?.title || 'Unknown' }}
      </span>
    </template>

      <!-- Assignee change -->
    <template v-else-if="item.ref_type === 'A'">
      assigned task to
      <strong>{{ item.user?.name || 'Unknown User' }}</strong>
    </template>

      <!-- Fallback -->
    <template v-else>
      updated the task
    </template>
  </span>
</template>

<script setup>
defineProps({
    item: {
        type: Object,
        required: true,
    },
});
</script>
