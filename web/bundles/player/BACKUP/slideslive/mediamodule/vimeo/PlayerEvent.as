package slideslive.mediamodule.vimeo
{
    import flash.events.*;

    public class PlayerEvent extends Event
    {
        private var _data:Object;
        public static const KEY_HOME:String = "key_home";
        public static const VIDEO_ERROR:String = "video_error";
        public static const HIDE_START:String = "hide_start";
        public static const VIDEO_PLAY_INITIATED:String = "video_play_initiated";
        public static const VIDEO_PLAY_COMPLETE:String = "video_play_complete";
        public static const SHOW_SIDEDOCK:String = "show_sidedock";
        public static const VIMEO_ERROR:String = "vimeo_error";
        public static const HIDE_CONTROLS:String = "hide_controls";
        public static const ENABLE_STAGE_VIDEO:String = "set_stage_video";
        public static const VIDEO_BUFFER_STOPPED:String = "video_buffer_stopped";
        public static const UNLOAD_VIDEO:String = "unload_video";
        public static const SHOW_CONTROLLER:String = "show_controller";
        public static const VIDEO_PROGRESS_PLAY:String = "video_progress_play";
        public static const KEY_ARROW_RIGHT:String = "key_arrow_right";
        public static const VIDEO_SCRUB_START:String = "video_scrub_start";
        public static const HIDE_VIDEO_INFO:String = "hide_video_info";
        public static const HIDE_OVERLAY:String = "hide_overlay";
        public static const KEY_ARROW_DOWN:String = "key_arrow_down";
        public static const VIDEO_IS_PASSWORD_PROTECTED:String = "video_is_password_protected";
        public static const FORCE_HIDE:String = "force_hide";
        public static const SHOW_COMPLETE:String = "show_complete";
        public static const KEY_E:String = "key_e";
        public static const VOLUME_SCRUB_MOVE:String = "volume_scrub_move";
        public static const VIDEO_BUFFER_FULL:String = "video_buffer_full";
        public static const FULLSCREEN_DISABLE:String = "fullscreen_disable";
        public static const KEY_F:String = "key_f";
        public static const FORCE_SHOW_CONTROLS:String = "force_show_controls";
        public static const KEY_W:String = "key_w";
        public static const NET_STREAM_TYPE_CHANGED:String = "net_stream_type_changed";
        public static const KEY_S:String = "key_s";
        public static const SIGNATURE_EXPIRED:String = "signature_expired";
        public static const LIKE_CLICKED:String = "like_clicked";
        public static const VIDEO_SCRUB_MOVE:String = "video_scrub_move";
        public static const HIDE_SIDEDOCK:String = "hide_sidedock";
        public static const OVERLAY_SHOW_COMPLETE:String = "overlay_show_complete";
        public static const VOLUME_SCRUB_START:String = "volume_scrub_start";
        public static const VOLUME_SCRUB_STOP:String = "volume_scrub_stop";
        public static const KEY_L:String = "key_l";
        public static const SCALE_CHANGED:String = "scale_changed";
        public static const SHOW_START:String = "show_start";
        public static const SET_LOOP:String = "set_loop";
        public static const HIDE_VIEW:String = "hide_view";
        public static const VIDEO_PLAY_UNLOAD:String = "video_play_unload";
        public static const SHARE_CLICKED:String = "share_clicked";
        public static const CHANGE_VOLUME:String = "change_volume";
        public static const EMBED_CLICKED:String = "embed_clicked";
        public static const HIDE_COMPLETE:String = "hide_complete";
        public static const FORCE_SHOW_SIDEDOCK:String = "force_show_sidedock";
        public static const PAUSE_PRESSED:String = "pause_pressed";
        public static const VIDEO_SCRUB_STOP:String = "video_scrub_stop";
        public static const VIDEO_BUFFERING:String = "video_buffering";
        public static const FULLSCREEN_ENABLE:String = "fullscreen_enable";
        public static const SHOW_VIDEO_INFO:String = "show_video_info";
        public static const WATCH_LATER_CLICKED:String = "watch_later_clicked";
        public static const FORCE_HIDE_CONTROLS:String = "force_hide_controls";
        public static const SHOW_VIEW:String = "show_view";
        public static const CHANGE_VIDEO_QUALITY:String = "change_video_quality";
        public static const SHOW_VIDEO_THUMBNAIL:String = "show_video_thumbnail";
        public static const VIDEO_PLAY:String = "video_play";
        public static const KEY_ARROW_UP:String = "key_arrow_up";
        public static const KEY_ARROW_LEFT:String = "key_arrow_left";
        public static const SET_VOLUME:String = "set_volume";
        public static const SHOW_CONTROLS:String = "show_controls";
        public static const HIDE_CONTROLLER:String = "hide_controller";
        public static const KEY_END:String = "key_end";
        public static const VOTE_CLICKED:String = "vote_clicked";
        public static const LOAD_VIDEO:String = "load_video";
        public static const HD_CLICKED:String = "hd_clicked";
        public static const RESET_CONTROLS:String = "reset_controls";
        public static const OVERLAY_HIDE_COMPLETE:String = "overlay_hide_complete";
        public static const VIDEO_BUFFER_STARTED:String = "video_buffer_started";
        public static const BAD_PASSWORD_SUBMITTED:String = "bad_password_submitted";
        public static const PLAYER_RESIZE:String = "player_resize";
        public static const VIDEO_IS_PRIVATE:String = "video_is_private";
        public static const PLAYER_FULLSCREEN:String = "player_fullscreen";
        public static const SEEK_VIDEO:String = "seek_video";
        public static const RENDER_STATE_CHANGED:String = "render_state_changed";
        public static const SET_SIZE:String = "set_size";
        public static const THUMBNAIL_LOADED:String = "thumbnail_loaded";
        public static const VIDEO_BUFFER_EMPTY:String = "video_buffer_empty";
        public static const SUBMIT_PASSWORD:String = "submit_password";
        public static const VIDEO_SEEK:String = "video_seek";
        public static const VIDEO_PROGRESS_LOAD:String = "video_progress_load";
        public static const KEY_SPACE:String = "key_space";
        public static const FORCE_SHOW:String = "force_show";
        public static const SCALE_CLICKED:String = "scale_clicked";
        public static const FORCE_HIDE_SIDEDOCK:String = "force_hide_sidedock";
        public static const CHANGE_COLOR:String = "change_color";
        public static const PLAY_PRESSED:String = "play_pressed";
        public static const VIDEO_PAUSE:String = "video_pause";
        public static const HIDE_VIDEO_THUMBNAIL:String = "hide_video_thumbnail";
        public static const SHOW_OVERLAY:String = "show_overlay";

        public function PlayerEvent(param1:String, param2 = null, param3:Boolean = false, param4:Boolean = false)
        {
            super(param1, param3, param4);
            this._data = param2;
            return;
        }// end function

        public function get data()
        {
            return this._data;
        }// end function

        override public function clone() : Event
        {
            return new PlayerEvent(type, this.data, bubbles, cancelable);
        }// end function

    }
}
