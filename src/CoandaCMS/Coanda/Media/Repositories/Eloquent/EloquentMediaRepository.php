<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent;

use Coanda, Config;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Media\Exceptions\MediaNotFound;
use CoandaCMS\Coanda\Media\Exceptions\MissingMedia;
use CoandaCMS\Coanda\Media\Exceptions\TagNotFound;

use CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\Media as MediaModel;
use CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\MediaTag as MediaTagModel;

use CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface;

use Carbon\Carbon;

/**
 * Class EloquentMediaRepository
 * @package CoandaCMS\Coanda\Media\Repositories\Eloquent
 */
class EloquentMediaRepository implements MediaRepositoryInterface {

    /**
     * @var Models\Media
     */
    private $model;
    /**
     * @var Models\MediaTag
     */
    private $tag_model;

    /**
     * @var \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @param MediaModel $model
     * @param MediaTagModel $tag_model
     * @param CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository
     */
    public function __construct(MediaModel $model, MediaTagModel $tag_model, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository)
	{
		$this->model = $model;
		$this->tag_model = $tag_model;
		$this->historyRepository = $historyRepository;
	}

    /**
     * @param $id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Media\Exceptions\MediaNotFound
     */
    public function findById($id)
	{
		$media = $this->model->find($id);

		if (!$media)
		{
			throw new MediaNotFound('Media #' . $id . ' not found');
		}
		
		return $media;
	}

    /**
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByIds($ids)
	{
		$media_list = new \Illuminate\Database\Eloquent\Collection;

		if (!is_array($ids))
		{
			return $media_list;
		}

		foreach ($ids as $id)
		{
			$media = $this->model->find($id);

			if ($media)
			{
				$media_list->add($media);
			}
		}

		return $media_list;
	}

    /**
     * @param $per_page
     * @return mixed
     */
    public function getList($per_page)
	{
		return $this->model->orderBy('created_at', 'desc')->paginate($per_page);
	}

    /**
     * @param $type
     * @param $per_page
     * @return mixed
     */
    public function getListByType($type, $per_page)
	{
		switch ($type)
		{
			case 'image':
			{
				return $this->model->where('mime', 'like', 'image/%')->orderBy('created_at', 'desc')->paginate($per_page);
			}

			case 'file':
			{
				return $this->model->where('mime', 'like', 'application/%')->orderBy('created_at', 'desc')->paginate($per_page);
			}

			default:
			{
				return $this->model->orderBy('created_at', 'desc')->paginate($per_page);
			}
		}
	}

    /**
     * @param $media_id
     */
    public function removeById($media_id)
	{
		$media = $this->findById($media_id);

		$media->delete();
	}

	private function createNewMediaItem($original_filename, $mime, $extension, $size)
	{
		$new_media = new $this->model;

		$new_media->original_filename = $original_filename;
		$new_media->mime = $mime;
		$new_media->extension = $extension;
		$new_media->size = $size;

		return $new_media;
	}

	private function generateUploadFileName($original, $extension)
	{
		return time() . '-' . md5($original) . '.' . $extension;
	}

	private function uploadPath()
	{
		return base_path() . '/' . Config::get('coanda::coanda.uploads_directory');
	}

    /**
     * @param $file
     * @return mixed
     */
    public function handleUpload($file)
	{
		$new_media = $this->createNewMediaItem($file->getClientOriginalName(), $file->getMimeType(), $file->getClientOriginalExtension(), $file->getClientSize());

		$upload_filename = $this->generateUploadFileName($new_media->original_filename, $file->getClientOriginalExtension());
		$upload_path = $this->uploadPath();

        $file->move($upload_path, $upload_filename);

        $new_media->filename = $upload_filename;

        if ($new_media->type == 'image')
        {
        	$dimensions = getimagesize($upload_path . '/' . $upload_filename);

        	$new_media->width = $dimensions[0];
        	$new_media->height = $dimensions[1];
        }

        $new_media->save();

        return $new_media;
	}

	public function fromURL($url)
	{
		$path_info = pathinfo($url);
		$file_name = $path_info['basename'];
		$extension = isset($path_info['extension']) ? $path_info['extension'] : false;

		if (!$extension)
		{
			return false;
		}
		
		$mime_type = $this->getMimeType($extension);

		$new_media = $this->createNewMediaItem($file_name, $mime_type, $extension, 0);

		$upload_filename = $this->generateUploadFileName($file_name, $extension);
		$upload_path = $this->uploadPath();

		if ($file_binary = file_get_contents($url))
		{
			file_put_contents($upload_path . '/' . $upload_filename, $file_binary);	

	        $new_media->filename = $upload_filename;

	        if ($new_media->type == 'image')
	        {
	        	$dimensions = getimagesize($upload_path . '/' . $upload_filename);

	        	$new_media->width = $dimensions[0];
	        	$new_media->height = $dimensions[1];
	        }

	        $new_media->size = filesize($upload_path . '/' . $upload_filename);

	        $new_media->save();

	        return $new_media;
		}

		return false;
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function downloadLink($media_id)
	{
		$media = $this->findById($media_id);

		return $media->originalFileLink();
	}

    /**
     * @param $per_page
     * @return mixed
     */
    public function tags($per_page)
	{
		return $this->tag_model->orderBy('created_at', 'desc')->paginate($per_page);
	}

    /**
     * @param $media_id
     * @param $tag_name
     */
    public function tagMedia($media_id, $tag_name)
	{
		$media = $this->findById($media_id);

		if ($tag_name && $tag_name !== '')
		{
			$tag_name = mb_strtolower($tag_name);

			$tag = $this->tag_model->whereTag($tag_name)->first();

			if (!$tag)
			{
				$tag = new $this->tag_model;
				$tag->tag = $tag_name;
				$tag->save();
			}

			$current_tags = $media->tags()->lists('media_tag_id');

			if (!is_array($current_tags))
			{
				$current_tags = [];
			}

			$current_tags[] = $tag->id;

			$tags = array_values(array_unique($current_tags));

			$media->tags()->sync($tags, true);
		}
	}

    /**
     * @param $media_id
     * @param $tag_id
     */
    public function removeTag($media_id, $tag_id)
	{
		$media = $this->findById($media_id);

		$media->tags()->detach($tag_id);
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function getTags($media_id)
	{
		$media = $this->findById($media_id);

		return $media->tags;
	}

    /**
     * @param $limit
     * @return mixed
     */
    public function recentTagList($limit)
	{
		return $this->tag_model->with('media')->orderBy('created_at', 'desc')->take($limit)->get();
	}

    /**
     * @param $tag_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Media\Exceptions\TagNotFound
     */
    public function tagById($tag_id)
	{
		$tag = $this->tag_model->find($tag_id);

		if (!$tag)
		{
			throw new TagNotFound('Tag #' . $tag_id . ' not found');
		}
		
		return $tag;
	}

    /**
     * @param $tag_id
     * @param $per_page
     * @return mixed
     */
    public function forTag($tag_id, $per_page)
	{
		$tag = $this->tagById($tag_id);

		return $tag->media()->orderBy('created_at', 'desc')->paginate($per_page);
	}

    /**
     * @return string
     */
    public function maxFileSize()
	{
		return ini_get('upload_max_filesize');
	}

	private function getMimeType($extension)
	{
	    $mimeTypes = array(

	    	"png" => "image/png",
	        "323"       => "text/h323",
	        "acx"       => "application/internet-property-stream",
	        "ai"        => "application/postscript",
	        "aif"       => "audio/x-aiff",
	        "aifc"      => "audio/x-aiff",
	        "aiff"      => "audio/x-aiff",
	        "asf"       => "video/x-ms-asf",
	        "asr"       => "video/x-ms-asf",
	        "asx"       => "video/x-ms-asf",
	        "au"        => "audio/basic",
	        "avi"       => "video/x-msvideo",
	        "axs"       => "application/olescript",
	        "bas"       => "text/plain",
	        "bcpio"     => "application/x-bcpio",
	        "bin"       => "application/octet-stream",
	        "bmp"       => "image/bmp",
	        "c"         => "text/plain",
	        "cat"       => "application/vnd.ms-pkiseccat",
	        "cdf"       => "application/x-cdf",
	        "cer"       => "application/x-x509-ca-cert",
	        "class"     => "application/octet-stream",
	        "clp"       => "application/x-msclip",
	        "cmx"       => "image/x-cmx",
	        "cod"       => "image/cis-cod",
	        "cpio"      => "application/x-cpio",
	        "crd"       => "application/x-mscardfile",
	        "crl"       => "application/pkix-crl",
	        "crt"       => "application/x-x509-ca-cert",
	        "csh"       => "application/x-csh",
	        "css"       => "text/css",
	        "dcr"       => "application/x-director",
	        "der"       => "application/x-x509-ca-cert",
	        "dir"       => "application/x-director",
	        "dll"       => "application/x-msdownload",
	        "dms"       => "application/octet-stream",
	        "doc"       => "application/msword",
	        "dot"       => "application/msword",
	        "dvi"       => "application/x-dvi",
	        "dxr"       => "application/x-director",
	        "eps"       => "application/postscript",
	        "etx"       => "text/x-setext",
	        "evy"       => "application/envoy",
	        "exe"       => "application/octet-stream",
	        "fif"       => "application/fractals",
	        "flr"       => "x-world/x-vrml",
	        "gif"       => "image/gif",
	        "gtar"      => "application/x-gtar",
	        "gz"        => "application/x-gzip",
	        "h"         => "text/plain",
	        "hdf"       => "application/x-hdf",
	        "hlp"       => "application/winhlp",
	        "hqx"       => "application/mac-binhex40",
	        "hta"       => "application/hta",
	        "htc"       => "text/x-component",
	        "htm"       => "text/html",
	        "html"      => "text/html",
	        "htt"       => "text/webviewhtml",
	        "ico"       => "image/x-icon",
	        "ief"       => "image/ief",
	        "iii"       => "application/x-iphone",
	        "ins"       => "application/x-internet-signup",
	        "isp"       => "application/x-internet-signup",
	        "jfif"      => "image/pipeg",
	        "jpe"       => "image/jpeg",
	        "jpeg"      => "image/jpeg",
	        "jpg"       => "image/jpeg",
	        "js"        => "application/x-javascript",
	        "latex"     => "application/x-latex",
	        "lha"       => "application/octet-stream",
	        "lsf"       => "video/x-la-asf",
	        "lsx"       => "video/x-la-asf",
	        "lzh"       => "application/octet-stream",
	        "m13"       => "application/x-msmediaview",
	        "m14"       => "application/x-msmediaview",
	        "m3u"       => "audio/x-mpegurl",
	        "man"       => "application/x-troff-man",
	        "mdb"       => "application/x-msaccess",
	        "me"        => "application/x-troff-me",
	        "mht"       => "message/rfc822",
	        "mhtml"     => "message/rfc822",
	        "mid"       => "audio/mid",
	        "mny"       => "application/x-msmoney",
	        "mov"       => "video/quicktime",
	        "movie"     => "video/x-sgi-movie",
	        "mp2"       => "video/mpeg",
	        "mp3"       => "audio/mpeg",
	        "mpa"       => "video/mpeg",
	        "mpe"       => "video/mpeg",
	        "mpeg"      => "video/mpeg",
	        "mpg"       => "video/mpeg",
	        "mpp"       => "application/vnd.ms-project",
	        "mpv2"      => "video/mpeg",
	        "ms"        => "application/x-troff-ms",
	        "mvb"       => "application/x-msmediaview",
	        "nws"       => "message/rfc822",
	        "oda"       => "application/oda",
	        "p10"       => "application/pkcs10",
	        "p12"       => "application/x-pkcs12",
	        "p7b"       => "application/x-pkcs7-certificates",
	        "p7c"       => "application/x-pkcs7-mime",
	        "p7m"       => "application/x-pkcs7-mime",
	        "p7r"       => "application/x-pkcs7-certreqresp",
	        "p7s"       => "application/x-pkcs7-signature",
	        "pbm"       => "image/x-portable-bitmap",
	        "pdf"       => "application/pdf",
	        "pfx"       => "application/x-pkcs12",
	        "pgm"       => "image/x-portable-graymap",
	        "pko"       => "application/ynd.ms-pkipko",
	        "pma"       => "application/x-perfmon",
	        "pmc"       => "application/x-perfmon",
	        "pml"       => "application/x-perfmon",
	        "pmr"       => "application/x-perfmon",
	        "pmw"       => "application/x-perfmon",
	        "pnm"       => "image/x-portable-anymap",
	        "pot"       => "application/vnd.ms-powerpoint",
	        "ppm"       => "image/x-portable-pixmap",
	        "pps"       => "application/vnd.ms-powerpoint",
	        "ppt"       => "application/vnd.ms-powerpoint",
	        "prf"       => "application/pics-rules",
	        "ps"        => "application/postscript",
	        "pub"       => "application/x-mspublisher",
	        "qt"        => "video/quicktime",
	        "ra"        => "audio/x-pn-realaudio",
	        "ram"       => "audio/x-pn-realaudio",
	        "ras"       => "image/x-cmu-raster",
	        "rgb"       => "image/x-rgb",
	        "rmi"       => "audio/mid",
	        "roff"      => "application/x-troff",
	        "rtf"       => "application/rtf",
	        "rtx"       => "text/richtext",
	        "scd"       => "application/x-msschedule",
	        "sct"       => "text/scriptlet",
	        "setpay"    => "application/set-payment-initiation",
	        "setreg"    => "application/set-registration-initiation",
	        "sh"        => "application/x-sh",
	        "shar"      => "application/x-shar",
	        "sit"       => "application/x-stuffit",
	        "snd"       => "audio/basic",
	        "spc"       => "application/x-pkcs7-certificates",
	        "spl"       => "application/futuresplash",
	        "src"       => "application/x-wais-source",
	        "sst"       => "application/vnd.ms-pkicertstore",
	        "stl"       => "application/vnd.ms-pkistl",
	        "stm"       => "text/html",
	        "svg"       => "image/svg+xml",
	        "sv4cpio"   => "application/x-sv4cpio",
	        "sv4crc"    => "application/x-sv4crc",
	        "t"         => "application/x-troff",
	        "tar"       => "application/x-tar",
	        "tcl"       => "application/x-tcl",
	        "tex"       => "application/x-tex",
	        "texi"      => "application/x-texinfo",
	        "texinfo"   => "application/x-texinfo",
	        "tgz"       => "application/x-compressed",
	        "tif"       => "image/tiff",
	        "tiff"      => "image/tiff",
	        "tr"        => "application/x-troff",
	        "trm"       => "application/x-msterminal",
	        "tsv"       => "text/tab-separated-values",
	        "txt"       => "text/plain",
	        "uls"       => "text/iuls",
	        "ustar"     => "application/x-ustar",
	        "vcf"       => "text/x-vcard",
	        "vrml"      => "x-world/x-vrml",
	        "wav"       => "audio/x-wav",
	        "wcm"       => "application/vnd.ms-works",
	        "wdb"       => "application/vnd.ms-works",
	        "wks"       => "application/vnd.ms-works",
	        "wmf"       => "application/x-msmetafile",
	        "wps"       => "application/vnd.ms-works",
	        "wri"       => "application/x-mswrite",
	        "wrl"       => "x-world/x-vrml",
	        "wrz"       => "x-world/x-vrml",
	        "xaf"       => "x-world/x-vrml",
	        "xbm"       => "image/x-xbitmap",
	        "xla"       => "application/vnd.ms-excel",
	        "xlc"       => "application/vnd.ms-excel",
	        "xlm"       => "application/vnd.ms-excel",
	        "xls"       => "application/vnd.ms-excel",
	        "xlsx"      => "vnd.ms-excel",
	        "xlt"       => "application/vnd.ms-excel",
	        "xlw"       => "application/vnd.ms-excel",
	        "xof"       => "x-world/x-vrml",
	        "xpm"       => "image/x-xpixmap",
	        "xwd"       => "image/x-xwindowdump",
	        "z"         => "application/x-compress",
	        "zip"       => "application/zip"
	    );

	    return isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : false;
	}	
}