<template>
  <div class="form-group">
    <label v-if="label">{{ label }}</label>
    <select class="form-control" :value="value" @input="$emit('input', $event.target.value)">
       <option v-for="opt in options" :key="opt.value" :value="opt.value">
         {{ opt.text }}
       </option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'BaseSelect',
  props: ['label', 'value', 'options']
}
</script>