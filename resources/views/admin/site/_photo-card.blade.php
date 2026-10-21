{{--
    One photo card. Used by edit.blade.php. It wraps <x-admin.photo-crop>.
    Needs: $ph = ['name', 'title', 'desc', 'help', 'w', 'h', 'max', 'long' (optional)], $site, $flash
    'w' and 'h' are the ratio of the frame the photo is shown in on the website. The photo-crop
    component saves exactly the framed area, so what you see in the preview is what is uploaded.
    'long' is the pixel size of the long side of the saved photo (default 1600).
--}}
<x-admin.photo-crop
    :name="$ph['name']" :title="$ph['title']" :description="$ph['desc']"
    :path="$site->{$ph['name']}"
    :upload-route="route('admin.site.image.upload', $ph['name'])"
    :delete-route="route('admin.site.image.destroy', $ph['name'])"
    :w="$ph['w']" :h="$ph['h']" :max-width="$ph['max']" :max-mb="5"
    :long="$ph['long'] ?? 1600"
    :help="$ph['help']"
    :message="$flash($ph['name'])" />