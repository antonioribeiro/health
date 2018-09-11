<template>
    <div :class="'col-md-'+resource.columnSize">
        <div :class="'btn btn-block btn-'+(target.result ? (target.result.healthy ? 'success' : 'danger') : 'secondary') +' '+resource.style.buttonLines"
             :title="resource.name"
             :data-name="resource.name"
             :style="'opacity: '+(!target.result || target.result.healthy ? resource.style.opacity.healthy : resource.style.opacity.failing)+';'"
        >
            <div v-if="resource.style.buttonLines === 'multi'">
                <p class="title">
                    {{ resource.name }}

                    <span class="question-mark" @click="$emit('show-result')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15px" viewBox="0 0 20 20"><path d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm2-13c0 .28-.21.8-.42 1L10 9.58c-.57.58-1 1.6-1 2.42v1h2v-1c0-.29.21-.8.42-1L13 9.42c.57-.58 1-1.6 1-2.42a4 4 0 1 0-8 0h2a2 2 0 1 1 4 0zm-3 8v2h2v-2H9z"/></svg>
                    </span>
                </p>

                <p class="subtitle">
                    <span v-if="target.name !== 'default'">
                        {{ target.display }}
                    </span>

                    <span v-else>&nbsp;</span>
                </p>

                <div class="row d-flex">
                    <div class="col-12 align-items-center">
                        <h3 @click="$emit('check-resource')" class="text-center" style="width: 1.7em; fill: white;">
                            <span v-if="!resource.loading && target.result && target.result.healthy">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM6.7 9.29L9 11.6l4.3-4.3 1.4 1.42L9 14.4l-3.7-3.7 1.4-1.42z"/></svg>
                            </span>

                            <span v-if="!resource.loading && target.result && !target.result.healthy">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/></svg>
                            </span>

                            <span v-if="!target.result || resource.loading">
                                <svg viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#fff"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="2"><circle stroke-opacity=".5" cx="18" cy="18" r="18"/><path d="M36 18c0-9.94-8.06-18-18-18"><animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/> </path></g></g></svg>
                            </span>
                        </h3>
                    </div>
                </div>
            </div>

            <h3 v-else>
                <i :class="'fa fa-'+(resource.style.opacity.healthy ? 'check-circle' : 'times-circle')"></i>
                {{ resource.name }}
            </h3>
        </div>
    </div>
</template>

<script>
export default {
  props: ['resource', 'target'],
}
</script>
